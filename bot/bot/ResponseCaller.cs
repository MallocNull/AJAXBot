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
                responseTypes = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.responses", StringComparison.Ordinal) && !t.FullName.Contains('+')).ToArray();
        }

        public static void callResponse(String responseName, string parameters, Message msg) {
            loadResponseTypes();
            responseTypes.First(t => t.Name == responseName).GetMethod("performOperation").Invoke(null, new Object[] { (object)parameters, (object)msg });
        }

        public static void callResponse(Type responseType, string parameters, Message msg) {
            loadResponseTypes();
            responseType.GetMethod("performOperation").Invoke(null, new Object[] { (object)parameters, (object)msg });
        }

        public static Type[] getResponseTypes() {
            loadResponseTypes();
            return responseTypes;
        }
    }
}
