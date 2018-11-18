var wshShell = new ActiveXObject("WScript.Shell");
wshShell.Run('Powershell.exe -ExecutionPolicy Bypass -File "E:\\php\\nest\\jobs\\humidity-alert.ps1"', 0, false);