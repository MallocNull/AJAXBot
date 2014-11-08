using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot {
    class ProtectionContext {
        private static bool isWriteable = true;
        private static DateTime timeOfWriteProtection = new DateTime(0);
        private static int maxProtectionTime = 45;

        public static bool Protect() {
            if(isWriteable == true || (DateTime.Now-timeOfWriteProtection).TotalSeconds > maxProtectionTime) {
                isWriteable = false;
                timeOfWriteProtection = DateTime.Now;
                return true;
            } else return false;
        }

        public static void Free() {
            isWriteable = true;
        }
    }
}
