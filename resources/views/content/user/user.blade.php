@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

@section('content')
	<h4 class="mb-4 py-3">
		<span class="text-muted fw-light">User Management /</span> Role Changer
	</h4>

	<!-- Basic Bootstrap Table -->
	<div class="card">
		<h5 class="card-header">Users</h5>
		<div class="table-responsive text-nowrap">
			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Email</th>
						<th>Type</th>
						<th>Subs Status</th>
						<th>Role</th>
						<th>Actions</th> <!-- Added this column for actions -->
					</tr>
				</thead>
				<tbody class="table-border-bottom-0">
					@foreach ($users as $user)
						<tr>
							<td>{{ $user->id }}</td> <!-- Added this column for ID -->
							<td>{{ $user->email }}</td>
							<td>
								{{ $user->user_type }}
							</td>
							@if ($user->subs_status > 0)
								<td><span class="badge bg-label-success me-1">{{ $user->subs_status }}</span></td>
							@else
								<td>{{ $user->subs_status }}</td>
							@endif
							<td>
								@dd($user)
								@if ($user->role->role_name == 'user')
									<span class="badge bg-primary text-white">{{ $user->role->role_name }}</span>
								@elseif ($user->role->role_name == 'admin')
									<span class="badge bg-warning">{{ $user->role->role_name }}</span>
								@elseif ($user->role->role_name == 'super admin')
									<span class="badge bg-black text-white">{{ $user->role->role_name }}</span>
								@endif
							</td>
							<td>
								<a href="" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
									data-bs-target="#modalRoleChanger">Change Role</a>

								<!-- Modal -->
								<form action="{{ route('changeRole', ['users' => $user->id]) }}" method="POST"
									class="modal fade" id="modalRoleChanger" tabindex="-1" aria-hidden="true">
									@csrf
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="modalCenterTitle">Change Role</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal"
													aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div action="" class="row g-2">
													<div class="col mb-0">
														<label for="roleWithTitle" class="form-label">Role</label>
														<select id="roleWithTitle" class="form-select" name="role_id">
															@foreach ($roles as $role)
																<option value="{{ $role->id }}"
																	@if ($user->role->role_name == $role->role_name) selected @endif>{{ $role->role_name }}</option>
															@endforeach
														</select>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-outline-secondary"
													data-bs-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary">Save changes</button>
											</div>
										</div>
									</div>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endsection
