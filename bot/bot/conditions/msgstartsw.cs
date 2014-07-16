using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class msgstartsw {
        static public string[] getInfo() {
            return new string[] { typeof(msgstartsw).Name, "message starts with" };
        }

        static public bool performCheck(Message msg, string parameter) {
            return msg.msg.ToLower().StartsWith(parameter.ToLower());
        }
    }
}
