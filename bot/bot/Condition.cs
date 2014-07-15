using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium;
using OpenQA.Selenium.Internal;
using OpenQA.Selenium.Support.UI;
using MySql.Data.MySqlClient;
using System.Threading;
using System.Reflection;

namespace bot {
    class Condition {
        public bool not;
        public Type conditionType;
        public string parameter;

        public Condition() { }

        public Condition(bool n, Type c, string p) {
            not = n;
            conditionType = c;
            parameter = p;
        }

        public Condition(bool n, int c, string p) {
            not = n; 
            string typeName = (string)(new MySqlCommand("SELECT `name` FROM `conditions` WHERE `id`=" + c, _G.conn)).ExecuteScalar();
            conditionType = Assembly.GetExecutingAssembly().GetTypes().Where(t => String.Equals(t.Namespace, "bot.conditions", StringComparison.Ordinal) && String.Equals(t.Name, typeName, StringComparison.Ordinal)).ToArray()[0];
            parameter = p;
        }
    }
}
