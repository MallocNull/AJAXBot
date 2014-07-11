using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class msgcontains {
        static public string[] getInfo() {
            return new string[] {typeof(msgcontains).Name, "message contains"};
        }
        
        static public bool performCheck(Message msg, string parameter) {
            return msg.msg.Contains(parameter);
        }
    }
}
