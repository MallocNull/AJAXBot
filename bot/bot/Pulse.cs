using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot {
    class Pulse {
        public static void updateHeartbeat() {
            string beat = _G.getLocalTimeFromUTC().ToString();
            Query.Quiet("UPDATE `updater` SET `heartbeat`='" + beat + "' WHERE `id`=1", _G.conn);
        }

        public static void checkUpdates() {
            var r = Query.Reader("SELECT * FROM `updater` WHERE `id`=1", _G.conn);
            r.Read();
            bool[] tmp = new bool[] { r.GetBoolean("responses"), r.GetBoolean("autonomous"), r.GetBoolean("config") };
            r.Close();
            if(tmp[0]) {
                Bot.loadResponseList();
                Query.Quiet("UPDATE `updater` SET `responses`=0 WHERE `id`=1", _G.conn);
            }
            if(tmp[1]) {
                // TODO implement update when autonomous is added
                Query.Quiet("UPDATE `updater` SET `autonomous`=0 WHERE `id`=1", _G.conn);
            }
            if(tmp[2]) {
                _G.loadConfig();
                Query.Quiet("UPDATE `updater` SET `config`=0 WHERE `id`=1", _G.conn);
            }
        }

        public static void pulseThread() {
            DateTime t = new DateTime(0);

            while(true) {
                if((DateTime.Now - t).TotalSeconds > 30) {
                    updateHeartbeat();
                    checkUpdates();
                    t = DateTime.Now;
                    Console.WriteLine("pulsed");
                }
            }
        }
    }
}
