using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;

namespace bot.responses {
    class jumble {
        static private Random rng = new Random();

        static public string[] getInfo() {
            return new string[] {typeof(jumble).Name, "Jumble Message",
                "Takes all words in the sentence and rearranges them randomly, sending the result to the chat."};
        }

        static public void performOperation(string parameters, Message msg) {
            List<String> mesg = new List<string>(msg.msg.ToLower().Replace(".", "").Replace("?", "").Replace("!", "").Replace(",", "").Replace("(whispers)", "").Split(' '));
            mesg = new List<string>(mesg.OrderBy((strval) => rng.Next()));

            StringBuilder b = new StringBuilder();
            foreach(String s in mesg)
                b.Append(s + " ");

            Chat.sendMessage(b.ToString());
        }
    }
}