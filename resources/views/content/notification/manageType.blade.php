@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

@section('content')
	<h4 class="mb-4 py-3">
		<span class="text-muted fw-light">Notifications /</span> Manage types
	</h4>

	<!-- Basic Bootstrap Table -->
	<div class="card">
		<h5 class="card-header">Notification Types
			<a href="#" class="btn btn-primary float-end" data-bs-toggle="modal"
				data-bs-target="#modalTambahType">
				<i class="menu-icon tf-icons bx bx bxs-plus-circle"></i> Tambah
			</a>

			{{-- modal --}}
			<form action="{{ route('add-type') }}" method="POST" class="modal fade" id="modalTambahType"
				tabindex="-1" aria-hidden="true">
				@csrf
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalCenterTitle">Tambah Notification Type</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="row g-2 mb-3">
								<div class="col mb-0">
									<label for="name" class="form-label">Name</label>
									<input id="name" class="form-control" type="text" name="name">
								</div>
							</div>
							<div class="row g-2 mb-3">
								<div class="col mb-0">
									<label for="description" class="form-label">Description</label>
									<textarea id="description" class="form-control" name="description"></textarea>
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
		</h5>
		<div class="table-responsive text-nowrap">
			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>name</th>
						<th>Actions</th> <!-- Added this column for actions -->
					</tr>
				</thead>
				<tbody class="table-border-bottom-0">
					@if ($notificationTypes->isEmpty())
						<tr>
							<td colspan="3" class="text-center">Tidak ada data notif type</td>
						</tr>
					@else
						@foreach ($notificationTypes as $notif)
							<tr>
								<td>{{ $notif->id }}</td> <!-- Added this column for ID -->
								<td>{{ $notif->name }}</td>
								<td>
									<button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
										data-bs-target="#modaledittype{{ $notif->id }}">Edit</button>

									{{-- modal edit --}}
									<form action="{{ route('edit-type', $notif->id) }}" method="POST" class="modal fade"
										id="modaledittype{{ $notif->id }}" tabindex="-1" aria-hidden="true">
										@csrf
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="modalCenterTitle">Tambah Notification Type</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal"
														aria-label="Close"></button>
												</div>
												<div class="modal-body">
													<div class="row g-2 mb-3">
														<div class="col mb-0">
															<label for="name" class="form-label">Name</label>
															<input id="name" class="form-control" type="text" name="name"
																value="{{ $notif->name }}">
														</div>
													</div>
													<div class="row g-2 mb-3">
														<div class="col mb-0">
															<label for="description" class="form-label">Description</label>
															<textarea id="description" class="form-control" name="description">{{ $notif->description }}</textarea>
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
									{{-- end modal edit --}}

									<a href="" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
										data-bs-target="#modalDeleteType{{ $notif->id }}">Hapus</a>

									{{-- modal delete --}}
									<form action="{{ route('delete-type', $notif->id) }}" method="POST" class="modal fade"
										id="modalDeleteType{{ $notif->id }}" tabindex="-1" aria-hidden="true">
										@csrf
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="modalCenterTitle">Hapus Notification Type</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal"
														aria-label="Close"></button>
												</div>
												<div class="modal-body">
													<div class="modal-body">
														<p style="word-wrap: break-word;">Apakah Anda yakin ingin menghapus type dengan nama
														</p>
														<p> {{ $notif->name }}?</p>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-outline-secondary"
														data-bs-dismiss="modal">Close</button>
													<button type="submit" class="btn btn-danger">Hapus</button>
												</div>
											</div>
										</div>
									</form>
									{{-- end modal delete --}}
								</td>
							</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
	</div>
@endsection

{{-- Modal --}}
