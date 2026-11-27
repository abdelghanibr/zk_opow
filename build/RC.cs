using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net.Sockets;
using System.Threading;
using System.Windows.Forms;

static class Program
{
    const int Port = 8000;
    static Process server;
    static NotifyIcon tray;

    [STAThread]
    static void Main()
    {
        bool created;
        using (var mutex = new Mutex(true, "RC_ZK_Attendance_Server", out created))
        {
            if (!created) { Open(); return; }   // déjà lancé : on ouvre seulement la page

            string dir = FindProject();
            if (dir == null)
            {
                MessageBox.Show("Dossier du projet introuvable (scripts\\start-service.ps1).", "RC", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!PortOpen())
            {
                var psi = new ProcessStartInfo("powershell.exe",
                    "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File \"" + Path.Combine(dir, "scripts\\start-service.ps1") + "\"");
                psi.WorkingDirectory = dir;
                psi.CreateNoWindow = true;
                psi.UseShellExecute = false;
                server = Process.Start(psi);
            }

            Application.EnableVisualStyles();
            tray = new NotifyIcon();
            tray.Icon = Icon.ExtractAssociatedIcon(Application.ExecutablePath);
            tray.Text = "RC - ZK Attendance (port " + Port + ")";
            var menu = new ContextMenuStrip();
            menu.Items.Add("Ouvrir l'application", null, delegate { Open(); });
            menu.Items.Add("Quitter (arrêter le serveur)", null, delegate { Quit(); });
            tray.ContextMenuStrip = menu;
            tray.DoubleClick += delegate { Open(); };
            tray.Visible = true;

            ThreadPool.QueueUserWorkItem(delegate
            {
                for (int i = 0; i < 40 && !PortOpen(); i++) Thread.Sleep(500);
                Open();
            });

            Application.Run();
        }
    }

    static string FindProject()
    {
        var d = new DirectoryInfo(AppDomain.CurrentDomain.BaseDirectory);
        for (int i = 0; i < 3 && d != null; i++, d = d.Parent)
            if (File.Exists(Path.Combine(d.FullName, "scripts\\start-service.ps1"))) return d.FullName;
        return null;
    }

    static bool PortOpen()
    {
        try { using (var c = new TcpClient()) { c.Connect("127.0.0.1", Port); return true; } }
        catch { return false; }
    }

    static void Open()
    {
        try { Process.Start(new ProcessStartInfo("http://127.0.0.1:" + Port) { UseShellExecute = true }); } catch { }
    }

    static void Quit()
    {
        try
        {
            if (server != null && !server.HasExited)
                Process.Start(new ProcessStartInfo("taskkill", "/PID " + server.Id + " /T /F") { CreateNoWindow = true, UseShellExecute = false }).WaitForExit();
        }
        catch { }
        tray.Visible = false;
        Application.Exit();
    }
}