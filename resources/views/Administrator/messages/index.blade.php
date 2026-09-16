@php 
    if(count($items))
    {
        $first_item = $items->first()->toArray();
    }
    else
    {
        $first_item = [];
    }
@endphp
@extends('layouts.admin')
@section('content')
<div class="">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>@lang('admin.inbox')</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <strong>@lang('admin.success')</strong>
                    </div>
                    @endif
                    <div class="row">
                        <!-- შეტყობინებები -->
                        @if(count($items))
                        <div class="col-sm-3 mail_list_column">
                            @foreach($items as $item)
                                @php 
                                    $time = strtotime($item->date);
                                    $newformat = date('Y-m-d',$time);
                                @endphp
                                <a href="#" 
                                   class="item-message"
                                   data-id="{{ $item->id }}"
                                   data-seen="{{ $item->seen }}"
                                   data-name="{{ $item->name }}"
                                   data-email="{{ $item->email }}"
                                   data-subject="{{ $item->subject }}"
                                   data-message="{{ $item->message }}"
                                   data-date="{{ $item->date }}"
                                >
                                    <div class="mail_list">
                                        <div class="left">
                                            @if(!$item->seen)
                                                <div class="tip cart-online-tip">
                                                    <div class="ringring"></div>
                                                    <div class="circle"></div>
                                                </div>
                                            @else
                                                <i class="fa fa-circle"></i> 
                                            @endif
                                            <i class="fas fa-times-circle remove-message" data-id="{{ $item->id }}"></i>
                                        </div>
                                        <div class="right">
                                            <h3>
                                                {{ $item->name }} <small>{{ $newformat }}</small>
                                            </h3>
                                            <p>{{ mb_substr($item->message,0,100,'UTF-8') . '...' }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            {{ $items->links() }}
                        </div>
                        <div class="col-sm-9 mail_view">
                            <div class="inbox-body">
                                <div class="mail_heading row">
                                    <div class="col-md-8">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary" type="button" id="compose">
                                                <i class="fa fa-reply"></i> Reply
                                            </button>
                                            <button class="btn btn-sm btn-default remove-message" data-id="{{ $item->id }}" type="button">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <p class="date" id="date"></p>
                                    </div>
                                    <div class="col-md-12">
                                        <h4 id="subject"></h4>
                                    </div>
                                </div>
                                <div class="sender-info">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <strong id="name"></strong>
                                            <span id="email"></span> 
                                        </div>
                                    </div>
                                </div>
                                <div class="view-mail" id="message"></div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning text-center">@lang('admin.no_messages')</div>
                        @endif
                        <!-- /შეტყობინებები -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(count($items))
<div class="compose col-md-6">
    <div class="compose-header">
        @lang('admin.new_message')
        <button type="button" class="close compose-close">
            <span>×</span>
        </button>
    </div>
    <form>
        <div class="compose-body">
            <div id="alerts"></div>
            <div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#editor">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font"><i class="fa fa-font"></i><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    </ul>
                </div>
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-edit="fontSize 5">
                                <p style="font-size:17px">Huge</p>
                            </a>
                        </li>
                        <li>
                            <a data-edit="fontSize 3">
                                <p style="font-size:14px">Normal</p>
                            </a>
                        </li>
                        <li>
                            <a data-edit="fontSize 1">
                                <p style="font-size:11px">Small</p>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="btn-group">
                    <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
                    <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
                    <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
                    <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
                </div>
                <div class="btn-group">
                    <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
                    <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
                    <a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fa fa-dedent"></i></a>
                    <a class="btn" data-edit="indent" title="Indent (Tab)"><i class="fa fa-indent"></i></a>
                </div>
                <div class="btn-group">
                    <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
                    <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
                    <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
                    <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
                </div>
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
                    <div class="dropdown-menu input-append">
                        <input class="span2" placeholder="URL" type="text" data-edit="createLink">
                        <button class="btn" type="button">Add</button>
                    </div>
                    <a class="btn" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-cut"></i></a>
                </div>
                <div class="btn-group">
                    <a class="btn" title="Insert picture (or just drag &amp; drop)" id="pictureBtn"><i class="fa fa-picture-o"></i></a>
                    <input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage">
                </div>
                <div class="btn-group">
                    <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
                    <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
                </div>
            </div>
            <div id="editor" class="editor-wrapper placeholderText" contenteditable="true"></div>
        </div>
        <input type="hidden" name="to" id="to">
        <div class="compose-footer">
            <button type="submit" id="send" class="btn btn-sm btn-success">
                @lang('admin.send')
            </button>
        </div>
    </form>
</div>
@endif
@endsection
@push('js')
<script>
    
    let firstItem = @json($first_item);
    
    if(Object.keys(firstItem).length > 0) 
    {
        $('#to').val(firstItem.email);
        $('#name').text(firstItem.name);
        $('#email').text('(' + firstItem.email + ')');
        $('#subject').text(firstItem.subject);
        $('#message').text(firstItem.message);
        $('#date').text(firstItem.date);
    }
    
    $('.item-message').on('click',function(){
        
        $('#to').val($(this).data('email'));
        $('#name').text($(this).data('name'));
        $('#email').text('(' + $(this).data('email') + ')');
        $('#subject').text($(this).data('subject'));
        $('#message').text($(this).data('message'));
        $('#date').text($(this).data('date'));
        
        let id = $(this).data('id');
        let seen = $(this).data('seen');
        let seenCircle = $(this).find('.fa-circle');
        let statusSeen = $(this).find('.left');

        if(!seen)
        {
            $.ajax({
            url : "{{ route('SeenMessages') }}",
            type : 'post',
            dataType : 'json',
            data : {id: id}
            })
            .done(function(data)
            {
                if(data.status) 
                {
                    //seenCircle.css('color','grey');
                    statusSeen.html("<i class='fa fa-circle'></i>");
                }
            });
        }
    });
    
    $('.remove-message').click(function(event){
        
        event.preventDefault();
        var id = $(this).data('id');
        
        bootbox.confirm({
            message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
            buttons: {
                confirm: {
                    label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                    className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
                },
                cancel: {
                    label: no,
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if(result) 
                {
                    window.location.href = '/admin/messages/remove/' + id;
                }
            }
        });
        
    });
       
</script>
@endpush
