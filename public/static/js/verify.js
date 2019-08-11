//js验证库：

var verify = {
	emsg:"ok"
	,getErr:function(){return this.emsg;}
    //用户名验证：v值，min最小长度(默认：3)，max最大长度（默认：10）
    ,isUsr: function (v, min, max) {
        var min = min || 3;
        var max = max || 10;

        if (min > max) {
            return false;
        }

        // var re = new RegExp("\\w+");	等价的	var re = /\w+/;

        var reg = new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5]{" + min + "," + max + "}$");
        if(!reg.test(v))
        {
	        this.emsg="用户名验证不通过！";
	        return false;
        }
        return true;

    }

    //密码验证：v值，min最小长度(默认：6)，max最大长度(默认：15)
    , isPwd: function (v, min, max) {
        var min = min || 6;
        var max = max || 15;

        if (min > max) {
            return false;
        }

        var reg = new RegExp("^[0-9a-zA-Z_.-]{" + min + "," + max + "}$");
        //console.log(reg.test(v));
        //return reg.test(v);

        if(!reg.test(v))
        {
	        this.emsg="密码验证不通过！";
	        return false;
        }
        return true;


    }

    //邮箱验证：帐户(至少3位)@主机(至少2位).类型(至少2位)
    , isEmail: function (v) {
        //	输出是否匹配
        var reg = /^[0-9a-zA-Z\u4e00-\u9fa5_.-]{3,}[@][0-9a-zA-Z_.-]{2,}([.][a-zA-Z]{2,}){1,2}$/;
        //return reg.test(v);

        if(!reg.test(v))
        {
	        this.emsg="邮箱验证不通过！";
	        return false;
        }
        return true;        
    }

    //手机号验证：
    , isMobile: function (v) {
        /**
         * 移动：134-139,  147,148    150-152,157-159,     172、178    182-184,187-188,    198
         * 联通：130-132、  145、146    155、156、 166、  171、175、176、   185-186、
         * 电信号段：133、    149、    153、  170、173、174、177、  180-181、189、    1349
         */

        var reg = RegExp("^[1](([3][0-9])|([4][5-9])|([5][0-3,5-9])|([6][56])|([7][0-8])|([8][0-9])|([9][189]))[0-9]{8}$");
        //return reg.test(v);
        
        if(!reg.test(v))
        {
	        this.emsg="手机号验证不通过！";
	        return false;
        }
        return true;
    }

    //身份证号验证：
    , isIDcard: function (v) {
        //15位或者18位：([1-9][0-9]{14})|([1-9][0-9]{16}[0-9xX])
        //		简化后：([1-9][0-9){14})([0-9]{2}[0-9xX])?

        //例：
        // 130503 670401 001的含义; 13为河北，05为邢台，03为桥西区，出生日期为1967年4月1日，顺序号为001
        //410521 1996 (0 5) (30)  203 3

        var reg = /^[1-9]\d{7}(([0][1-9])|([1][0-2]))(([0][1-9])|([1-2]\d)|([3][0-1]))\d{3}$|^[1-9]\d{5}[1-9]\d{3}(([0][1-9])|([1][0-2]))(([0][1-9])|([1-2]\d)|([3][0-1]))\d{3}[0-9xX]$/;
        //return reg.test(v)

        if(!reg.test(v))
        {
	        this.emsg="身份证号验证不通过！";
	        return false;
        }
        return true;

    }

}

