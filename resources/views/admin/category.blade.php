@extends('admin.master')
@section('content')
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户中心 <span
                class="c-gray en">&gt;</span> 用户管理 <a class="btn btn-success radius r mr-20"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="pd-20">
        {{--<div class="text-c"> 日期范围：
            <input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;">
            -
            <input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;">
            <input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、电话、邮箱" id="" name="">
            <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
        </div>--}}
        <div class="cl pd-5 bg-1 bk-gray mt-20"><span class="l">{{--<a href="javascript:;" onclick="datadel()"
                                                                   class="btn btn-danger radius"><i
                            class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>--}} <a href="javascript:;"
                                                                          onclick="category_add('添加类别','/admin/category_add','','510')"
                                                                          class="btn btn-primary radius"><i
                            class="Hui-iconfont">&#xe600;</i> 添加类别</a></span> <span class="r">共有数据：<strong>{{ count($categorys) }}</strong> 条</span>
        </div>
        <div class="mt-20">
            <table class="table table-border table-bordered table-hover table-bg table-sort">
                <thead>
                <tr class="text-c">
                    <th width="25"><input type="checkbox" name="" value=""></th>
                    <th width="80">ID</th>
                    <th width="100">名称</th>
                    <th width="40">编号</th>
                    <th width="90">父类别</th>
                    <th width="50">预览图</th>
                    <th width="100">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categorys as $category)
                    <tr class="text-c">
                        <td><input type="checkbox" value="1" name=""></td>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->category_no }}</td>
                        <td>
                            @if($category->parent != null)
                                {{ $category->parent->name }}
                            @endif
                        </td>
                        <td>
                            @if($category->preview != null)
                                <img src="{{$category->preview}}" alt="" style="width: 50px; height: 50px;">
                            @endif
                        </td>
                        <td class="td-manage">{{--<a style="text-decoration:none" onClick="member_stop(this,'10001')"
                                                 href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>--}}
                            <a title="编辑" href="javascript:;" onclick="category_edit('编辑类别','/admin/category_edit?id={{$category->id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                            <a title="删除" href="javascript:;" onclick='category_del("{{$category->name}}", "{{$category->id}}")' class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('my-js')
    <script>
        function category_add(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function category_edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function category_del(name, id) {
            layer.confirm('确认要删除【' + name +'】吗？',function(index){
                //此处请求后台程序，下方是成功后的前台处理……
                $.ajax({
                    type: 'post', // 提交方式 get/post
                    url: '/admin/service/category/del', // 需要提交的 url
                    dataType: 'json',
                    data: {
                        id: id,
                        _token: "{{csrf_token()}}"
                    },
                    success: function(data) {
                        if(data == null) {
                            layer.msg('服务端错误', {icon:2, time:2000});
                            return;
                        }
                        if(data.status != 0) {
                            layer.msg(data.message, {icon:2, time:2000});
                            return;
                        }

                        layer.msg(data.message, {icon:1, time:2000});
                        location.replace(location.href);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                        layer.msg('ajax error', {icon:2, time:2000});
                    },
                    beforeSend: function(xhr){
                        layer.load(0, {shade: false});
                    }
                });
            });
        }
    </script>
@endsection