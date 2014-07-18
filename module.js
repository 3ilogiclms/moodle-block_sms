// JavaScript Document

M.block_sms = {};
M.block_sms.init = function(Y, param3) {

    // Variables.
    var showuser = Y.one("#btnajax");
    var sms_send = Y.one('#smssend');
    var action = Y.one('#id_r_id');
    var action1 = Y.one('#id_m_id');
    var action2 = Y.one('#id_c_id');
    var action3 = Y.one('#id_g_selection');
    var action4 = Y.one('#id_g_id');


    var userlist = Y.one("#table-change");
    var img = Y.one('#load');

    // Body load first time
    var msg_body = Y.one('#id_sms_body');
    var m_id = action1.get('value');



    Y.io('load_message.php?m_id=' + m_id, {
        on: {
            start: function(id, args) {
                msg_body.hide();
                img.show();

            },
            complete: function(id, e) {
                var json = e.responseText;
                console.log(json);
                img.hide();
                msg_body.show();
                msg_body.set('value', json);
            }
        }
    });

    // Image default setting.
    img.hide();
    sms_send.hide();

    // Event occurs after click on show user button.
    showuser.on('click', function() {
        var content = Y.one('#id_sms_body');
        var c_id = action2.get('value');
        var r_id = action.get('value');
        var msg = content.get('value');

        // Group ID
        var action4_value = action4.get('value');
        var action3_value = action3.get('value');
        Y.io('user_list.php?msg=' + msg + '&c_id=' + c_id + '&r_id=' + r_id + '&g_id=' + action4_value + '&isgroup=' + action3_value, {
            on: {
                start: function(id, args) {
                    userlist.set('innerHTML', '<img src="Loading.gif" id="load-users" style="margin-left:6cm;" />');
                },
                complete: function(id, e) {
                    var json = e.responseText;
                    console.log(json);
                    userlist.set('innerHTML', json);
                    sms_send.show();
                }
            }
        });
    });
    // End show user event.

    // If viewpage is 2 means send sms page.
    if (param3 == '2') {
        action.on('change', function() {
            var b = this.get('text');
        });

        // Select Message Template.
        action1.on('change', function() {
            var content = Y.one('#id_sms_body');
            var m_id = action1.get('value');
            Y.io('load_message.php?m_id=' + m_id, {
                on: {
                    start: function(id, args) {
                        content.hide();
                        img.show();
                    },
                    complete: function(id, e) {
                        var json = e.responseText;
                        console.log(json);
                        img.hide();
                        content.show();
                        content.set('value', json);
                    }
                }
            });
        });

        // Coruse Change & check group
        var action2_value = action2.get('value');

        action2.on('change', function() {
            var action2_value = action2.get('value');

            Y.io('proxy.php?c_id=' + action2_value, {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var t_id = Y.one('#id_g_selection');
                        var json = e.responseText;

                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        t_id.set('innerHTML', asd);
                    }
                }
            });

        });

        // Select group option Show list of groups.

        // Coruse Change & check group
        action3.on('change', function() {
            var action2_value = action2.get('value');

            Y.io('proxy.php?c_id=' + action2_value + '&group=2', {
                on: {
                    start: function(id, args) {
                    },
                    complete: function(id, e) {
                        var t_id = Y.one('#id_g_id');
                        var json = e.responseText;

                        console.log(json);
                        var test = json.split("^");
                        var asd = "";
                        for (i = 0; i < test.length - 1; i++) {
                            var sep = test[i].split("~");
                            asd += '<option value = ' + sep[0] + '>' + sep[1] + '</option>';
                        }
                        t_id.set('innerHTML', asd);
                    }
                }
            });

        });


    }

};
