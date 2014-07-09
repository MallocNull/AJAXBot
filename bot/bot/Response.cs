using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;
using MySql.Data.MySqlClient;

namespace bot {
    class Response {
        public string conditions;
        public Type responseType;
        public string parameters;
        public int cooldown;
        public int lastCall;

        public Response(string conditions, string responseType, string parameters, int cooldown) {
            this.conditions = conditions;
            this.responseType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && String.Equals(t.Name, responseType, StringComparison.Ordinal)).ToArray()[0];
            this.parameters = parameters;
            this.cooldown = cooldown;
            this.lastCall = 0;
        }

        public Response(string conditions, int responseId, string parameters, int cooldown) {
            this.conditions = conditions;
            string typeName = (string)(new MySqlCommand("SELECT `name` FROM `resptypes` WHERE `id`=" + responseId, _G.conn)).ExecuteScalar();
            this.responseType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && String.Equals(t.Name, typeName, StringComparison.Ordinal)).ToArray()[0];
            this.parameters = parameters;
            this.cooldown = cooldown;
            this.lastCall = 0;
        }
    }
}
