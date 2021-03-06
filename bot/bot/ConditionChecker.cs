﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;

namespace bot {
    class ConditionChecker {
        static Type[] conditionTypes = null;

        static void loadConditionTypes() {
            if(conditionTypes == null)
                conditionTypes = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.conditions", StringComparison.Ordinal) && !t.FullName.Contains('+')).ToArray();
        }

        public static bool checkCondition(String conditionName, Message msg, string parameter) {
            loadConditionTypes();
            return (bool)conditionTypes.First(t => t.Name == conditionName).GetMethod("performCheck").Invoke(null, new Object[] { (object)msg, (object)parameter });
        }

        public static bool checkCondition(Type conditionType, Message msg, string parameter) {
            loadConditionTypes();
            return (bool)conditionType.GetMethod("performCheck").Invoke(null, new Object[] { (object)msg, (object)parameter });
        }

        public static Type[] getConditions() {
            loadConditionTypes();
            return conditionTypes;
        }
    }
}
