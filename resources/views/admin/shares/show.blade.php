@extends('adminlte::page')

@section('title', 'Share Audio Detail')

@section('content_header')
    Share Audio Detail
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('share_audio_detail', $audio->id) }}
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
                    <label class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $audio->title }}</p>
                    </div>
                </div>

                <!-- Duration -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Duration</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $audio->duration }}</p>
                    </div>
                </div>


                <!-- Station Title -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Station Title</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $audio->station->title }}</p>
                    </div>
                </div>

                <!-- Uploaded By -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Uploaded By</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $audio->volunteer->name }}</p>
                    </div>
                </div>

                <!-- Uploaded Date -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Uploaded Date</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $audio->created_at }}</p>
                    </div>
                </div>
            </div>

              <!-- Note -->
              <div class="card-header with-border">
                <h3 class="card-title"><b>Note</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-sm-10">
                        <div class="row push-top">
                                <div class="col-md-12">
                                    <p class="form-control-plaintext">{{ $audio->note }}</p>
                                    <input type="hidden" id="note-val" value="{{ $audio->note }}">
                                    <input type="hidden" id="title-val" value="{{ $audio->title }}">
                                </div>
                                <!-- @if(isset($audio->note))
                                    @if($audio->note !== " ")
                                    <div class="col-md-3" style="margin-top:30px;">
                                    <button type="button" id="dwn-note" class="btn btn-default"><i class="fa fa-download"></i> Download Note</button>
                                    </div>
                                    @endif
                                @endif -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Image</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-sm-10">
                        <div class="row push-top">
                            @foreach ($banners as $banner)
                                <div class="col-md-10">
                                    <div class="thumbnail">
                                        <a href="{{ $banner['data'] }}" data-fancybox="gallery">
                                            <img src="{{ $banner['data'] }}" class="img-responsive" style="max-width:300px;">
                                        </a>
                                    </div>
                                </div>
                                <!-- @if(isset($banner['data']))
                                    @if($banner['data'] !== " ")
                                    <div class="col-md-3" style="margin-top:30px;">
                                        <a href="{{$banner['data']}}" target="_blank" rel="noopener" download><button type="button" class="btn btn-default"><i class="fa fa-download"></i> Download Image</button></a>
                                    </div>
                                    @endif
                                @endif -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recording -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Audio</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-sm-10">
                        <div class="row push-top">
                            @foreach ($recordings as $recording)
                                <div class="col-md-10">
                                    <div class="thumbnail">
                                        <audio controls preload="none">
                                            <source src="{{ $recording['data'] }}">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                                <!-- @if(isset($recording['data']))
                                    @if($recording['data'] !== " ")
                                    <div class="col-md-3" style="margin-top:30px;">
                                        <a href="{{$recording['data']}}" target="_blank" rel="noopener" download><button type="button" class="btn btn-default"><i class="fa fa-download"></i> Download Audio</button></a>
                                    </div>
                                    @endif
                                @endif -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <a href="{{ route('admin.shares.index') }}" class="btn btn-default" role="button">Back</a>
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

</script>
@endsection

@section('css')
<style>
.card-body {
    min-height: auto;
}
</style>
@endsection
