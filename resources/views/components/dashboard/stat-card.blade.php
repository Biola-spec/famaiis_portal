@props([
    'label',
    'value',
    'icon' => 'mdi-chart-box',
    'variant' => 'primary',
])

<div class="dashboard-stat-col col-lg-4 col-md-6 col-12">
    <div class="box overflow-hidden pull-up stat-card-{{ $variant }}">
        <div class="box-body">
            <div class="stat-card-wrapper">
                <div>
                    <p class="stat-card-title">{{ $label }}</p>
                    <h3 class="stat-card-number">{{ $value }}</h3>
                </div>
                <div class="stat-icon-box">
                    <i class="font-size-24 mdi {{ $icon }}"></i>
                </div>
            </div>
        </div>
    </div>
</div>
