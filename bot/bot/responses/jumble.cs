using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;

namespace bot.responses {
    class jumble {
        static public string[] getInfo() {
            return new string[] {"junble"/*typeof(jumble).Name*/, "Jumble Message",
                "Takes all words in the sentence and rearranges them randomly, sending the result to the chat."};
        }

        static public void performOperation(string parameters, Message msg) {

        }
    }
}