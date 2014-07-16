using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class nameis {
        static public string[] getInfo() {
            return new string[] { typeof(nameis).Name, "username is" };
        }

        static public bool performCheck(Message msg, string parameter) {
            return msg.name.ToLower() == parameter.ToLower();
        }
    }
}
