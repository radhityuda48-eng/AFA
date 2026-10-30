(function () {
  "use strict";

  // Compress product photos in the browser before submitting. This avoids PHP
  // UPLOAD_ERR_INI_SIZE (code 1) for large phone-camera images on shared hosting.
  var MAX_BYTES = 450 * 1024; // target <= 450 KB to suit restrictive hosting limits
  var MAX_EDGE = 1400;

  function formatSize(bytes) {
    return bytes >= 1024 * 1024
      ? (bytes / (1024 * 1024)).toFixed(2) + " MB"
      : Math.round(bytes / 1024) + " KB";
  }

  function loadImage(file) {
    return new Promise(function (resolve, reject) {
      var url = URL.createObjectURL(file);
      var img = new Image();
      img.onload = function () { URL.revokeObjectURL(url); resolve(img); };
      img.onerror = function () { URL.revokeObjectURL(url); reject(new Error("Browser tidak dapat membaca foto ini.")); };
      img.src = url;
    });
  }

  function canvasBlob(canvas, quality) {
    return new Promise(function (resolve) {
      canvas.toBlob(function (blob) { resolve(blob); }, "image/jpeg", quality);
    });
  }

  async function compressImage(file) {
    // Small JPEG files are kept as-is; PNG/WEBP/large photos are converted to JPEG.
    if (file.type === "image/jpeg" && file.size <= MAX_BYTES) return file;
    var img = await loadImage(file);
    var scale = Math.min(1, MAX_EDGE / Math.max(img.naturalWidth, img.naturalHeight));
    var canvas = document.createElement("canvas");
    canvas.width = Math.max(1, Math.round(img.naturalWidth * scale));
    canvas.height = Math.max(1, Math.round(img.naturalHeight * scale));
    var ctx = canvas.getContext("2d", { alpha: false });
    if (!ctx) throw new Error("Browser tidak mendukung pemrosesan foto.");
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

    var quality = 0.84;
    var blob = await canvasBlob(canvas, quality);
    while (blob && blob.size > MAX_BYTES && quality > 0.42) {
      quality -= 0.08;
      blob = await canvasBlob(canvas, quality);
    }
    while (blob && blob.size > MAX_BYTES && canvas.width > 700 && canvas.height > 700) {
      canvas.width = Math.max(1, Math.round(canvas.width * 0.82));
      canvas.height = Math.max(1, Math.round(canvas.height * 0.82));
      ctx = canvas.getContext("2d", { alpha: false });
      ctx.fillStyle = "#ffffff";
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      quality = 0.78;
      blob = await canvasBlob(canvas, quality);
    }
    if (!blob) throw new Error("Gagal mengompres foto. Coba simpan foto sebagai JPG lalu pilih kembali.");
    return new File([blob], (file.name.replace(/\.[^.]+$/, "") || "produk") + ".jpg", {
      type: "image/jpeg", lastModified: Date.now()
    });
  }

  function initProductImagePreview() {
    var input = document.getElementById("imageInput");
    var preview = document.getElementById("imagePreview");
    var remove = document.getElementById("removePreview");
    var status = document.getElementById("uploadStatus");
    var form = input && input.form;
    if (!input || !preview) return;

    var objectUrl = null;
    var compressedFile = null;
    var processing = false;
    var submitting = false;

    function emptyState() {
      if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
      compressedFile = null;
      preview.innerHTML = '<div class="preview-placeholder"><span>PREVIEW FOTO</span><small>Foto akan otomatis diperkecil sebelum di-upload.</small></div>';
      if (status) status.textContent = "Belum ada foto dipilih";
      if (remove) remove.hidden = true;
    }

    input.addEventListener("change", async function () {
      var file = input.files && input.files.length ? input.files[0] : null;
      compressedFile = null;
      if (!file) { emptyState(); return; }
      if (file.type.indexOf("image/") !== 0) {
        alert("File bukan gambar yang didukung. Pilih JPG, PNG, atau WEBP.");
        input.value = ""; emptyState(); return;
      }
      if (objectUrl) URL.revokeObjectURL(objectUrl);
      objectUrl = URL.createObjectURL(file);
      var img = document.createElement("img");
      img.alt = "Preview foto produk";
      img.onload = function () { preview.innerHTML = ""; preview.appendChild(img); };
      img.src = objectUrl;
      if (status) status.textContent = "Memproses foto otomatis… ukuran awal " + formatSize(file.size);
      if (remove) remove.hidden = false;
      processing = true;
      input.disabled = true;
      try {
        compressedFile = await compressImage(file);
        if (status) status.textContent = "✓ Foto siap: " + formatSize(compressedFile.size) + (compressedFile !== file ? " (sudah dikompres otomatis)" : "");
      } catch (err) {
        if (status) status.textContent = "Foto belum diproses. Coba pilih JPG/JPEG.";
        alert((err && err.message ? err.message : "Foto tidak dapat diproses.") + " Jika file iPhone HEIC, ubah ke JPG terlebih dahulu.");
      } finally {
        processing = false;
        input.disabled = false;
      }
    });

    if (remove) remove.addEventListener("click", function () { input.value = ""; emptyState(); });

    if (form) form.addEventListener("submit", function (event) {
      if (submitting) return;
      if (!input.files || !input.files.length || !compressedFile) {
        event.preventDefault();
        alert("Pilih foto dan tunggu sampai proses kompresi selesai sebelum menyimpan.");
        return;
      }
      if (processing) { event.preventDefault(); alert("Tunggu hingga foto selesai diproses."); return; }
      event.preventDefault();
      submitting = true;
      var transfer = new DataTransfer();
      transfer.items.add(compressedFile);
      input.files = transfer.files;
      if (status) status.textContent = "Mengunggah foto terkompresi (" + formatSize(compressedFile.size) + ")…";
      form.submit();
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initProductImagePreview);
  else initProductImagePreview();
})();
