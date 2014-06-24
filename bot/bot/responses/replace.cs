using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;

namespace bot.responses {
    class replace {
        static public string[] getInfo() {
            return new string[] {typeof(replace).Name, "Replace Phrase",
                "Takes a message, replaces all instances of the specified phrase, and sends it to the chat."};
        }

        static public void performOperation(string parameters, Message msg) {

        }
    }
}