using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using MySql.Data.MySqlClient;

namespace bot {
    static class Query {
        static object Scalar(string query, MySqlConnection conn) {
            return (new MySqlCommand(query, conn)).ExecuteScalar();
        }

        static void Quiet(string query, MySqlConnection conn) {
            (new MySqlCommand(query, conn)).ExecuteNonQuery();
        }

        static MySqlDataReader Reader(string query, MySqlConnection conn) {
            // TODO write this
        }
    }
}
