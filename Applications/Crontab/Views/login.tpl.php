<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="utf-8">
  <title>WorkerMan-crontab管理界面</title>
    <style>
        body {
          font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
          font-size: 14px;
          line-height: 1.428571429;
          color: #333;
          background-color: #fff;
        }
        h1, .h1 {
          font-size: 24px;
          font-weight: 500;
        }
        .column{
            width:25%;
            margin: 0 auto;
        }
        .form-group{
            margin-bottom: 15px;
        }
        label {
          display: inline-block;
          margin-bottom: 5px;
          font-weight: bold;
        }
        .form-control {
          display: block;
          width: 100%;
          height: 34px;
          padding: 6px 12px;
          font-size: 14px;
          line-height: 1.428571429;
          color: #555;
          vertical-align: middle;
          background-color: #fff;
          background-image: none;
          border: 1px solid #ccc;
          border-radius: 4px;
          -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
          box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
          -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
          transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }
        .btn-default {
          color: #333;
          background-color: #fff;
          border-color: #ccc;
        }
        .btn {
          display: inline-block;
          padding: 6px 12px;
          margin-bottom: 0;
          font-size: 14px;
          font-weight: normal;
          line-height: 1.428571429;
          text-align: center;
          white-space: nowrap;
          vertical-align: middle;
          cursor: pointer;
          background-image: none;
          border: 1px solid transparent;
          border-radius: 4px;
          -webkit-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          -o-user-select: none;
          user-select: none;
          border-color:#adadad;
        }
        .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open .dropdown-toggle.btn-default {
          color: #333;
          background-color: #ebebeb;
          border-color: #adadad;
        }
        .alert-danger {
          color: #b94a48;
          background-color: #f2dede;
          border-color: #ebccd1;
        }
        .alert-dismissable {
          padding-right: 35px;
        }
        .alert-dismissable {
          padding-right: 35px;
        }
        .alert {
          padding: 15px;
          margin-bottom: 20px;
          border: 1px solid transparent;
          border-radius: 4px;
        }
        .alert h4 {
          margin-top: 0;
          color: inherit;
        }
        .alert-dismissable .close {
          position: relative;
          top: -2px;
          right: 21px;
          color: inherit;
        }
        
        button.close {
          padding: 0;
          cursor: pointer;
          background: transparent;
          border: 0;
          -webkit-appearance: none;
        }
        .close {
          float: right;
          font-size: 21px;
          font-weight: bold;
          line-height: 1;
          color: #000;
          text-shadow: 0 1px 0 #fff;
          opacity: .2;
          filter: alpha(opacity=20);
        }
        .footer {
          margin: 8px auto 0 auto;
          padding-bottom: 8px;
          color: #666;
          font-size: 12px;
          text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-4 column">
        </div>
        <div class="col-md-4 column">
        <?php if(!empty($msg)){?>
            <div class="alert alert-dismissable alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4>
                    <?php echo $msg;?>
                </h4> 
            </div>
        <?php }?>
            <h1>WorkerMan-crontab管理员登录</h1>
            <form role="form" method="POST" action="">
                <div class="form-group">
                     <label>用户名</label><input type="text" name="admin_name" class="form-control" />
                </div>
                <div class="form-group">
                     <label for="exampleInputPassword1">密码</label><input type="password" name="admin_password"  class="form-control" id="exampleInputPassword1" />
                </div>
                <button type="submit" class="btn btn-default">登录</button>
            </form>
        </div>
        <div class="col-md-4 column">
        </div>
    </div>
</div>
<div class="footer">Powered by <a href="http://www.workerman.net" target="_blank"><strong>Workerman!</strong></a></div>
</body>
</html>
