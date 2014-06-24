using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using MySql.Data.MySqlClient;

namespace bot {
    static class Query {
        static public object Scalar(string query, MySqlConnection conn) {
            try {
                return (new MySqlCommand(query, conn)).ExecuteScalar();
            } catch(Exception e) {
                Console.WriteLine(e.Message);
                return null;
            }
        }

        static public bool Quiet(string query, MySqlConnection conn) {
            try {
                (new MySqlCommand(query, conn)).ExecuteNonQuery();
                return true;
            } catch(Exception e) {
                Console.WriteLine(e.Message);
                return false;
            }
        }

        static public MySqlDataReader Reader(string query, MySqlConnection conn) {
            try {
                return (new MySqlCommand(query, conn)).ExecuteReader();
            } catch(Exception e) {
                Console.WriteLine(e.Message);
                return null;
            }
        }
    }
}
