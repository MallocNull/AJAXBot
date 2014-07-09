using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot {
    class Sanitizer {
        public static String Sanitize(String str) {
            return str.Replace("'", "\\'");
        }
    }
}
