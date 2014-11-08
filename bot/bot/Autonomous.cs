using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;
using MySql.Data.MySqlClient;

namespace bot {
    class Autonomous {
        public int id;
        public string name;
        public int startDay;
        public int[] startTime;
        public int periodicity;
        public int randomness;
        public Type responseType;
        public string parameters;

        public DateTime nextTrigger = new DateTime(0);
        public DateTime linkTrigger = new DateTime(0);

        public int autolinkid;
        public Autonomous autolink;
        public bool linkrespond;
        public int timeout;
        public int torandomness;

        public Random rng = new Random();

        public Autonomous(int id, string name, int startDay, int[] startTime, int periodicity, int randomness,
                          string responseType, string parameters,
                          int autolinkid, bool linkrespond, int timeout, int torandomness) {
            this.id = id;
            this.name = name;
            this.startDay = startDay;
            this.startTime = startTime;
            this.periodicity = periodicity;
            this.randomness = randomness;

            this.responseType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && String.Equals(t.Name, responseType, StringComparison.Ordinal)).ToArray()[0];
            this.parameters = parameters;

            this.autolinkid = autolinkid;
            this.linkrespond = linkrespond;
            this.timeout = timeout;
            this.torandomness = torandomness;

            CalculateNextTrigger();
        }

        public Autonomous(int id, string name, int startDay, int[] startTime, int periodicity, int randomness,
                          int responseId, string parameters,
                          int autolinkid, bool linkrespond, int timeout, int torandomness) {
            this.id = id;
            this.name = name;
            this.startDay = startDay;
            this.startTime = startTime;
            this.periodicity = periodicity;
            this.randomness = randomness;

            string responseType = (string)(new MySqlCommand("SELECT `name` FROM `resptypes` WHERE `id`=" + responseId, _G.conn)).ExecuteScalar();
            this.responseType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && String.Equals(t.Name, responseType, StringComparison.Ordinal)).ToArray()[0];
            this.parameters = parameters;

            this.autolinkid = autolinkid;
            this.linkrespond = linkrespond;
            this.timeout = timeout;
            this.torandomness = torandomness;
            
            CalculateNextTrigger();
        }

        public void triggerRoutine(bool force = false) {
            if(_G.getLocalTimeFromUTC() > nextTrigger || force) {
                ResponseCaller.callResponse(responseType, parameters, new Message("null","null"));
                CalculateNextTrigger();
                if(autolinkid != -1) {
                    linkTrigger = _G.getLocalTimeFromUTC().AddSeconds(timeout + (rng.Next(2) == 0 ? 1 : -1) * rng.Next(torandomness));
                    Bot.ignoreResponses = linkrespond;
                }
            } else if(_G.getLocalTimeFromUTC() > linkTrigger && linkTrigger != new DateTime(0)) {
                autolink.triggerRoutine(true);
                linkTrigger = new DateTime(0);
                Bot.ignoreResponses = false;
            }
        }

        public void CalculateNextTrigger() {
            DateTime now = _G.getLocalTimeFromUTC();

            if(nextTrigger == new DateTime(0)) {
                if(startDay == -2)
                    nextTrigger = now.AddSeconds(periodicity + (rng.Next(2) == 0 ? 1 : -1) * rng.Next(randomness));
                else if(startDay == -1) {
                    if(startTime[0] == -1)
                        nextTrigger = now;
                    else {
                        if(now.Hour < startTime[0] || (now.Hour == startTime[0] && now.Minute < startTime[1]))
                            nextTrigger = new DateTime(now.Year, now.Month, now.Day, startTime[0], startTime[1], 0);
                        else {
                            DateTime tomorrow = now.AddDays(1);
                            nextTrigger = new DateTime(tomorrow.Year, tomorrow.Month, tomorrow.Day, startTime[0], startTime[1], 0);
                        }
                    }
                } else if(startDay == -999)
                    nextTrigger = now.AddYears(1000);
                else {
                    DateTime tmp;
                    if((int)now.DayOfWeek == startDay - 1) {
                        if(now.Hour < startTime[0] || (now.Hour == startTime[0] && now.Minute < startTime[1]))
                            nextTrigger = new DateTime(now.Year, now.Month, now.Day, startTime[0], startTime[1], 0);
                        else {
                            tmp = now.AddDays(7);
                            nextTrigger = new DateTime(tmp.Year, tmp.Month, tmp.Day, startTime[0], startTime[1], 0);
                        }
                    } else {
                        int adder = (startDay - 1) - (int)now.DayOfWeek;
                        tmp = now.AddDays(adder);
                        if(adder < 0) tmp.AddDays(7);
                        nextTrigger = new DateTime(tmp.Year, tmp.Month, tmp.Day, startTime[0], startTime[1], 0);
                    }
                }
            } else if(startDay != -999)
                nextTrigger = now.AddSeconds(periodicity + (rng.Next(2) == 0 ? 1 : -1) * rng.Next(randomness));
        }
    }
}
