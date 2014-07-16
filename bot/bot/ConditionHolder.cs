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
                    if(pair[0] == -1) {
                        try {
                            conditions.Add(new Condition((tk[1] == "1") ? true : false, Int32.Parse(tk[2]), tk[3], Int32.Parse(c[on + 1])));
                        } catch(Exception err) {
                            conditions.Add(new Condition((tk[1] == "1") ? true : false, Int32.Parse(tk[2]), tk[3], -1));
                        }
                    }
                    if(Int32.Parse(tk[0]) > 0 && searching == -1) {
                        conditions.Clear();
                        searching = 0;
                        pair[0] = i;
                        if(i != 0) {
                            if(pair[1] == -1) {
                                var tmpmpmpmtkoj = condstring.Substring(0, _G.indexOfNthCommand(condstring, i));
                                sequence.Add(new ConditionHolder(tmpmpmpmtkoj));
                            } else if(pair[1] != i-1) {
                                var tmpmpmpmtkoj = _G.Substring(condstring, _G.indexOfNthCommand(condstring, pair[1]+1), _G.indexOfNthCommand(condstring, i));
                                sequence.Add(new ConditionHolder(tmpmpmpmtkoj));
                            }
                        }
                    }
                    if(searching != -1)
                        searching += Int32.Parse(tk[0]) - Int32.Parse(tk[4]);
                    if(searching == 0) {
                        searching = -1;
                        pair[1] = i;
                        string str = "";

                        try {
                            str = _G.Substring(condstring, (pair[0] == 0) ? 0 : _G.indexOfNthCommand(condstring, pair[0]), _G.indexOfNthCommand(condstring, pair[1]+1));
                        } catch(Exception err) {
                            str = _G.Substring(condstring, (pair[0] == 0) ? 0 : _G.indexOfNthCommand(condstring, pair[0]), condstring.Length);
                        }
                        str = Int32.Parse(str.Substring(0, str.IndexOf(','))) - 1 + str.Substring(str.IndexOf(','));

                        int concatval = -1;
                        if(str[str.Length - 3] == ';') {
                            concatval = Int32.Parse(str.Substring(str.Length - 2, 1));
                            str = str.Substring(0, str.Length - 2);
                        }

                        var tmpmpmp = str.Substring(str.LastIndexOf(',') + 1, (str.LastIndexOf(';') - (str.LastIndexOf(',') + 1)));

                        str = str.Substring(0, str.LastIndexOf(',') + 1) + (Int32.Parse(str.Substring(str.LastIndexOf(',') + 1, (str.LastIndexOf(';') - (str.LastIndexOf(',') + 1)))) - 1) + ";" + ((concatval==-1)?"":concatval+";");

                        sequence.Add(new ConditionHolder(str, false));
                    }
                    i++;
                }
            }

            if(pair[0] != -1 && pair[1] != i-1) {
                var tmkmfdknk = _G.Substring(condstring, _G.indexOfNthCommand(condstring, pair[1]+1), condstring.Length);
                sequence.Add(new ConditionHolder(tmkmfdknk, false));
            }

            return;
        }

        public bool calculateValue(Message msg, out int lastOperator) {
            if(conditions.Count != 0)
                lastOperator = conditions.Last().op;
            else
                sequence.Last().calculateValue(msg, out lastOperator);
            return calculateValue(msg);
        }

        public bool calculateValue(Message msg) {
            bool retval = false;
            if(conditions.Count != 0) {
                for(int i = 0; i < conditions.Count; i++) {
                    if(i == 0)
                        retval = conditions[i].evaluateCondition(msg);
                    else {
                        switch(conditions[i - 1].op) {
                            case 0:
                                retval = retval && conditions[i].evaluateCondition(msg);
                                break;
                            case 1:
                                retval = retval || conditions[i].evaluateCondition(msg);
                                break;
                        }
                    }
                }
            } else {
                int lastOp = -1;
                for(int i = 0; i < sequence.Count; i++) {
                    if(i == 0)
                        retval = sequence[i].calculateValue(msg, out lastOp);
                    else {
                        switch(lastOp) {
                            case 0:
                                retval = retval && sequence[i].calculateValue(msg, out lastOp);
                                break;
                            case 1:
                                retval = retval || sequence[i].calculateValue(msg, out lastOp);
                                break;
                        }
                    }
                }
            }
            return retval;
        }
    }
}
