using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class msgcntword {
        static public string[] getInfo() {
            return new string[] { typeof(msgcntword).Name, "message contains phrase" };
        }

        static public bool performCheck(Message msg, string parameter) {
            return false; // implement
        }
    }
}
