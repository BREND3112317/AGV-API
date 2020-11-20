using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace TestForm
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private object _logLocker = new object();

        private void button1_Click(object sender, EventArgs e)
        {
            string url = "http://59.124.226.9:6592/AGV/SendAgvCmd";
            
            HttpWebRequest req = (HttpWebRequest)WebRequest.Create(url);
            req.ContentType = "application/json";
            req.Method = "POST";

            JObject send = new JObject();
            send.Add(new JProperty("Name", "ITRI_3-4"));//測試用
            send.Add(new JProperty("Cmd", "500"));
            send.Add(new JProperty("Param", new int[] { }));
            string input = send.ToString();
            byte[] data = Encoding.ASCII.GetBytes(input);
            Stream reqStream = req.GetRequestStream();
            reqStream.Write(data, 0, data.Length);
            StreamReader sr = new StreamReader(req.GetResponse().GetResponseStream(), Encoding.UTF8);
            string responseStr = sr.ReadToEnd();          
            //SendReturn agvCfg = JsonConvert.DeserializeObject<SendReturn>(responseStr); 請同學實作
            Log(responseStr);
        }
        
        private void Log(string message)
        {
            msgRTB.Text = DateTime.Now.ToString("[HH:mm:ss] ") + message + Environment.NewLine + msgRTB.Text;

            lock (_logLocker)
            {
                if (!Directory.Exists(Application.StartupPath + "\\Log"))
                    Directory.CreateDirectory(Application.StartupPath + "\\Log");

                using (StreamWriter sw = new StreamWriter(Application.StartupPath + "\\Log\\" + DateTime.Now.ToString("yyyy-MM-dd") + ".txt", true))
                {
                    sw.WriteLine(DateTime.Now.ToString("[HH:mm:ss] ") + message);
                }
            }
        }
    }
}
