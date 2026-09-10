@if($model->status === 'active')
<span class="icp-badge icp-badge-success">Active</span>
@else
<span class="icp-badge icp-badge-muted">Inactive</span>
@endif
