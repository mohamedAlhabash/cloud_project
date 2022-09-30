@extends('backend.layouts.app')
@section('content')
    <!-- Start cache setting  -->
    <section class="cache-setting container">

        <div class="card">
            <form action="{{route('storeCacheConfig')}}" method="POST">
                @csrf
                <div class="policy">
                    <p>Replacement Policy:</p>
                    <select name="policy" id="">
                        <option value="">
                            Select Policy
                            <option value="Random_Replacement">Random Replacement</option>
                            <option value="Least_Recently_Used">Least Recently Used</option>
                        </option>
                    </select>
                </div>

                <div class="capacity">
                    <label for="range">Capacity Cache:</label>
                    <input type="number" name="capacity" id="">
                </div>

                <div class="btns">
                    <button type="reset" id="clear" class="btn">Clear</button>
                    <button type="submit" class="btn">OK</button>
                </div>

            </form>
        </div>
    </section>
    <!-- End cache setting  -->
@stop

@section('script')
    <script>
        let clearbtn = document.getElementById('clear');
        clearbtn.onclick = () => {
            $.ajax({
            type: 'GET',
            url: '{{ route('clearCache') }}',
            data: null,
            success: function (data, status) {
                alert(data.message);
            },
            error: function (data) {
                console.log(data);
            }
        });
        }

    </script>
@stop
