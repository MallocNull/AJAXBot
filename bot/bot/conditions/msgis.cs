using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class msgis {
        static public string[] getInfo() {
            return new string[] { typeof(msgis).Name, "message is" };
        }

        static public bool performCheck(Message msg, string parameter) {
            return msg.msg.ToLower() == parameter.ToLower();
        }
    }
}
