const { app, BrowserWindow, shell } = require('electron');
const path = require('path');
const APP_URL = 'https://afaciptakaryanusantara.site/';
const ALLOWED = new Set(['afaciptakaryanusantara.site','www.afaciptakaryanusantara.site']);
let win;
function createWindow() {
  win = new BrowserWindow({width:1360,height:900,minWidth:900,minHeight:650,show:false,autoHideMenuBar:true,backgroundColor:'#101010',
    webPreferences:{nodeIntegration:false,contextIsolation:true,sandbox:true,webviewTag:false,spellcheck:true}});
  win.once('ready-to-show',()=>win.show());
  win.webContents.setWindowOpenHandler(({url})=>{
    try { const u=new URL(url); if(u.protocol==='https:'&&ALLOWED.has(u.hostname)) win.loadURL(url);
      else if(['https:','mailto:','tel:'].includes(u.protocol)) shell.openExternal(url); } catch(_){}
    return {action:'deny'};
  });
  win.webContents.on('will-navigate',(event,url)=>{
    try {const u=new URL(url); if(u.protocol!=='https:'||!ALLOWED.has(u.hostname)){event.preventDefault();if(['https:','mailto:','tel:'].includes(u.protocol))shell.openExternal(url);}}
    catch(_){event.preventDefault();}
  });
  win.loadURL(APP_URL).catch(()=>win.loadFile(path.join(__dirname,'offline-fallback.html')));
}
app.whenReady().then(()=>{createWindow();app.on('activate',()=>{if(BrowserWindow.getAllWindows().length===0)createWindow();});});
app.on('window-all-closed',()=>{if(process.platform!=='darwin')app.quit();});
