@extends('backend.layouts.app')
@section('content')
    <!-- Start statistics  -->
    <section class="statistics container">
        <div class="card">

            <div class="items">
                <span>Number Of Items In Cache : 50 </span>
            </div>

            <div class="stats">
                <div class="stats-box">
                    <div class="stats-name">
                        <div class="before"></div> Hit Rate
                    </div>
                    <div class="stats-progress">
                        <span data-progress="80%"></span>
                    </div>
                </div>


                <div class="stats-box">
                    <div class="stats-name">
                        <div class="before"></div> Miss Rate
                    </div>
                    <div class="stats-progress">
                        <span data-progress="50%"></span>
                    </div>
                </div>


                <div class="stats-box">
                    <div class="stats-name">
                        <div class="before"></div> Cache Utilization
                    </div>
                    <div class="stats-progress">
                        <span data-progress="80%"></span>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <!-- End statistics  -->
@stop
