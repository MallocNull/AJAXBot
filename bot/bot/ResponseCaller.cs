using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;

namespace bot {
    static class ResponseCaller {
        static Type[] responseTypes = null;

        static void loadResponseTypes() {
            if(responseTypes == null)
                responseTypes = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal)).ToArray();
        }

        public static void callResponse(String responseName) {
            loadResponseTypes();
            //responseTypes[0].GetMethod("test").Invoke(null, "");
        }

        public static Type[] getResponseTypes() {
            loadResponseTypes();
            return responseTypes;
        }
    }
}
