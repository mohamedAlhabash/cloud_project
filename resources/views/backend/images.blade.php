@extends('backend.layouts.app')
@section('content')
    <!-- Start Upload section -->
    <section class="upload container">

        <div class="card">
            <form action="{{ route('showImage') }}" method="POst">
                @csrf
                <div>
                    <label for="key">Key</label>
                    <input type="text" name="key" id="key" value="{{ old('key') }}" placeholder="Enter Your Key">
                    @error('key')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn submit">Ok submit</button>
            </form>
        </div>
        <div class="preview-image">
            <img width="500" height="500"
                src="{{ $attachment ? asset('uploads/' . $attachment->value) : asset('backend/img/no-image.jpg') }}">
        </div>


    </section>
    <!-- End Upload section -->
@stop
