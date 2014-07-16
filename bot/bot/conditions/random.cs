using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class random {
        static private Random rng = new Random();

        static public string[] getInfo() {
            return new string[] { typeof(random).Name, "random, 1 in " };
        }

        static public bool performCheck(Message msg, string parameter) {
            int chance;
            try {
                chance = Int32.Parse(parameter);
            } catch(Exception err) { return false; }
            if(chance <= 1) return true;
            else {
                int pick = (int)Math.Round((double)((chance-1) / 2));
                if(rng.Next(chance) == pick) return true;
                else return false;
            }
        }
    }
}
