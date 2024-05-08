@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Principal</h1>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
                <div class="inner">
                    
                    <h3>S/. 12000</h3>
                    <p>Monto en caja</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-warning">
                <div class="inner">
                    @php $contador_de_usuarios=0; @endphp
                    @foreach($usuarios as $usuario)
                        @php $contador_de_usuarios++; @endphp
                    @endforeach
                    <h3>{{$contador_de_usuarios}}</h3>
                    <p>Usuarios registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    
                    <h3>60</h3>
                    <p>Prestamos activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-danger">
                <div class="inner">
                    
                    <h3>30</h3>
                    <p>Cuotas Vencidas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-3 col-6">

            <div class="small-box bg-secondary">
                <div class="inner">
                    
                    <h3>S/. 12000</h3>
                    <p>Saldo Vigente en caja</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-primary">
                <div class="inner">
                    @php $contador_de_usuarios=0; @endphp
                    @foreach($usuarios as $usuario)
                        @php $contador_de_usuarios++; @endphp
                    @endforeach
                    <h3>{{$contador_de_usuarios}}</h3>
                    <p>Clientes Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-dark">
                <div class="inner">
                    
                    <h3>S/. 15 060</h3>
                    <p>Cartera Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="{{url('/admin/usuarios')}}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        
    </div>

@endsection
