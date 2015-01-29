using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions
{
    class charcheck
    {
        static public string[] getInfo()
        {
            return new string[] { typeof(charcheck).Name, "char code exceeds" };
        }

        static public bool performCheck(Message msg, string parameter)
        {
            bool jarakar = false;
            foreach (Char c in msg.msg) {
                if (c > Int32.Parse(parameter))
                {
                    jarakar = true;
                    break;
                }
            }
            return jarakar;
        }
    }
}
