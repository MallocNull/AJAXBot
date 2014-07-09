using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.conditions {
    class contains {
        static public string[] getInfo() {
            return new string[] {typeof(contains).Name, "contains"};
        }

        static public bool performCheck(string check, string parameter) {
            return check.Contains(parameter);
        }
    }
}
