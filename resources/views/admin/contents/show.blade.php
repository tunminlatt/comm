@extends('adminlte::page')

@section('title', 'Content Detail')

@section('content_header')
    Content Detail
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('content_detail', $content->id) }}
@endsection

@section('content')
    <div class="card">
        <form>
            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $content->title }}</p>
                    </div>
                </div>

                <!-- Station Title -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Station Title</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $content->station->title }}</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{!! $content->description !!}</p>
                    </div>
                </div>

                <!-- Uploaded Date -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Uploaded Date</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $content->created_at }}</p>
                    </div>
                </div>

                <!-- Name -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $content->name }}</p>
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Phone Number</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $content->phone }}</p>
                    </div>
                </div>

                <!-- Contact Method -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Contact Method</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $contactMethod }}</p>
                    </div>
                </div>
            </div>


            <!-- File -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>File</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-sm-10">
                        <div class="row push-top">
                            @foreach ($files as $file)
                                @if(isset($file['data']))
                                    <div class="col-md-3">
                                        <a href="{{ route('admin.contents.file', $content->id) }}"><button type="button" class="btn btn-default"><i class="fa fa-download"></i> Download File</button></a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <a href="{{ route('admin.contents.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')

<script>
function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
}

// Start file download.
document.getElementById("dwn-note").addEventListener("click", function(){
    // Generate download of hello.txt file with some content
    var text = document.getElementById("note-val").value;
    var title = document.getElementById("title-val").value;

    var filename = title+" Note.txt";

    download(filename, text);
}, false);

function audioDownload(path, title) {
    var file = filename(path)
    var afterDot = file.substr(file.indexOf('.'))
    $.ajax({
        type: 'GET',
        crossDomain: true,
        dataType: 'jsonp',
        url: path,
            xhrFields: {
            responseType: 'blob',
        },
        headers: {
            'Access-Control-Allow-Origin': "*",
            'Access-Control-Allow-Methods': 'GET, PUT, POST, DELETE, HEAD, OPTIONS'
        },
        success: function(data){
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = title+""+afterDot;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    })
}

function filename(path){
    path = path.substring(path.lastIndexOf("/")+ 1);
    return (path.match(/[^.]+(\.[^?#]+)?/) || [])[0];
}

function imageDownload(path, title) {
    var file = filename(path)
    var afterDot = file.substr(file.indexOf('.'))
    $.ajax({
        type: 'GET',
        crossDomain: true,
        dataType: 'jsonp',
        url: path,
            xhrFields: {
            responseType: 'blob',
        },
        headers: {
            'Access-Control-Allow-Origin': "*",
            'Access-Control-Allow-Methods': 'GET, PUT, POST, DELETE, HEAD, OPTIONS'
        },
        success: function(data){
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = title+""+afterDot;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    })
}

function downloadFile(urlToSend, title) {
    $.ajax({
        url: urlToSend,
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = title;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    });
}

</script>
@endsection

@section('css')
<style>
.card-body {
    min-height: auto;
}
</style>
@endsection
