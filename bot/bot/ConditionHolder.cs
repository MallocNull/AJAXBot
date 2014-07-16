using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot {
    class ConditionHolder {
        List<Condition> conditions = new List<Condition>();
        List<ConditionHolder> sequence = new List<ConditionHolder>();

        public ConditionHolder(string condstring, bool baselvl = true) {
            List<string> c = condstring.Split(';').ToList();
            c.RemoveAt(c.Count - 1);
            int i = 0, searching = -1;
            int[] pair = { -1, -1 };
            for(int on = 0; on < c.Count; on++) {
                string cond = c[on];
                List<string> tk = cond.Split(',').ToList();
                if(tk.Count > 2) {
                    if(Int32.Parse(tk[0]) > 0 && searching == -1) {
                        searching = 0;
                        pair[0] = i;
                    }
                    if(searching != -1)
                        searching += Int32.Parse(tk[0]) - Int32.Parse(tk[4]);
                    if(searching == 0) {
                        searching = -1;
                        pair[1] = i;
                        string str = "", tmp = "";

                        if(pair[0] != 0) {
                            //tmp = condstring.Substring(0, 
                        } else {

                        }

                        str = condstring.Substring((pair[0] == 0) ? 0 : _G.indexOfNth(condstring, ';', pair[0] + 1) + 1, _G.indexOfNth(condstring, ';', pair[1] + 2) + 1);
                        str = Int32.Parse(str.Substring(0, str.IndexOf(','))) - 1 + str.Substring(str.IndexOf(','));

                        sequence.Add(new ConditionHolder(str, false));
                    }
                    i++;
                }
            }
        }

        public bool calculateValue(Message msg) {
            return true;
        }
    }
}
