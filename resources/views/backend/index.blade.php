@extends('backend.layouts.app')
@section('content')
    <!-- Start Upload section -->
    <section class="upload container">
        <div class="card">
            <form action="{{route('storeImage')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="key">Key</label>
                    <input type="text" name="key" id="key" placeholder="Enter Your Key">
                    @error('key')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="second">
                    <label for="upload" class="i-lable">Upload</label>
                    <input type="file" name="value" id="upload" placeholder="Enter Your image" >
                    @error('value')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn submit">Ok submit</button>
            </form>
        </div>
        <div class="preview-image">
            <img src="{{ asset('backend/img/no-image.jpg') }}">{{--script--}}
        </div>
    </section>
    <!-- End Upload section -->
@endsection
