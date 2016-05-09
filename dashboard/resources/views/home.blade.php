@extends('layout.master')
@section('title', 'Meotrics')
@section('style')

@endsection
@section('script')
@endsection

@section('content')
	<!--THIS IS HOME {{cookie('laravel_session')}}-->
    <div class="row">
        <div class="card col-md-12">
            <div class="row">
                <div class="content col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Number of visitors</p>
                                <h3>150</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Increased <i class="fa fa-sort-up"></i></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>User Growth rate</p>
                                <h3>50%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Decreased <i class="fa fa-sort-down"></i></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Retention rate </p>
                                <h3>25%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Increased <i class="fa fa-sort-up"></i></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Purchase Conversion rate</p>
                                <h3>80</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Decreased <i class="fa fa-sort-down"></i></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Total revenue</p>
                                <h3>100</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Increased <i class="fa fa-sort-up"></i></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Revenue per customer</p>
                                <h3>20</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Increased <i class="fa fa-sort-up"></i></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Abandonment rate</p>
                                <h3>90</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Increased <i class="fa fa-sort-up"></i></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Highest revenue campaign</p>
                                <h3>Google campaign</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Most effective Referrer</p>
                                <h3>Youtube</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('additional')
  @include('partials/install_guide')
@endsection
