using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;

namespace bot.responses {
    class sendmsg {
        static public string[] getInfo() {
            return new string[] {typeof(sendmsg).Name, "Send Message",
                "Sends a message or messages. New lines seperate strings, causing the bot to pick one randomly. To break up a string into multiple messages, seperate clauses with the accent grave (`). {0} is replaced by the message sender's name."};
        }

        static public void performOperation(string parameters, Message msg) {
            
        }
    }
}