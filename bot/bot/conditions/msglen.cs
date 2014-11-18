using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class msglen {
        static public string[] getInfo() {
            return new string[] { typeof(msglen).Name, "message length over" };
        }

        static public bool performCheck(Message msg, string parameter) {
            return msg.msg.Length >= Int32.Parse(parameter);
        }
    }
}
