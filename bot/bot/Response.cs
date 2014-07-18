using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;
using MySql.Data.MySqlClient;

namespace bot {
    class Response {
        public ConditionHolder conditions;
        public string condstr;
        public Type responseType;
        public string parameters;
        public int cooldown;
        public DateTime lastCall;

        public Response(string conditions, string responseType, string parameters, int cooldown) {
            this.conditions = new ConditionHolder(conditions);
            this.condstr = conditions;
            this.responseType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && String.Equals(t.Name, responseType, StringComparison.Ordinal)).ToArray()[0];
            this.parameters = parameters;
            this.cooldown = cooldown;
            this.lastCall = new DateTime(0);
        }

        public Response(string conditions, int responseId, string parameters, int cooldown) {
            this.conditions = new ConditionHolder(conditions);
            this.condstr = conditions;
            string typeName = (string)(new MySqlCommand("SELECT `name` FROM `resptypes` WHERE `id`=" + responseId, _G.conn)).ExecuteScalar();
            this.responseType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && String.Equals(t.Name, typeName, StringComparison.Ordinal)).ToArray()[0];
            this.parameters = parameters;
            this.cooldown = cooldown;
            this.lastCall = new DateTime(0);
        }

        public bool triggerResponse(Message msg) {
            if(cooldown != -1) {
                if((DateTime.Now - lastCall).TotalSeconds < cooldown)
                    return false;
            }
            if(msg.name.ToLower() != _G.username.ToLower()) {
                if(conditions.calculateValue(msg)) {
                    ResponseCaller.callResponse(responseType, parameters, msg);
                    lastCall = DateTime.Now;
                    return true;
                } 
            }
            return false;
        }
    }
}
