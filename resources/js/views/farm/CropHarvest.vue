
<template>
  <Master>
    <section class="section dashboard">
      <!-- Page heading -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h4 class="mb-1">Crop Harvests</h4>
          <p class="text-muted mb-0">
            Harvest records, expenses and production trends.
          </p>
        </div>

        <button class="btn btn-success rounded-pill"
                style="background: darkgreen; border-color: darkgreen"
                @click="openAddModal">
          <i class="bi bi-plus-circle me-1"></i> Record Harvest
        </button>
      </div>

      <!-- Filters -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Harvest Filters</h5>

          <div class="row g-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label">Crop</label>
              <select v-model="filters.crop_id" class="form-select">
                <option value="">All crops</option>
                <option v-for="crop in crops" :key="crop.id"
                        :value="String(crop.id)">
                  {{ crop.crop_name }}
                  {{ crop.variety ? ` - ${crop.variety}` : '' }}
                </option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Season</label>
              <select v-model="filters.season" class="form-select">
                <option value="">All seasons</option>
                <option v-for="season in availableSeasons"
                        :key="season" :value="season">
                  {{ season }}
                </option>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">Period</label>
              <select v-model="filters.period" class="form-select"
                      @change="applyPeriod">
                <option value="all">All time</option>
                <option value="today">Today</option>
                <option value="week">This week</option>
                <option value="month">This month</option>
                <option value="year">This year</option>
                <option value="custom">Custom dates</option>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">From</label>
              <input type="date" class="form-control"
                     v-model="filters.date_from"
                     :disabled="filters.period !== 'custom'">
            </div>

            <div class="col-md-2">
              <label class="form-label">To</label>
              <input type="date" class="form-control"
                     v-model="filters.date_to"
                     :disabled="filters.period !== 'custom'">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
              <button class="btn btn-success"
                      style="background: darkgreen; border-color: darkgreen"
                      @click="applyFilters">
                Apply Filters
              </button>
              <button class="btn btn-outline-secondary"
                      @click="resetFilters" title="Reset filters">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary cards -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Total Harvested</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-basket"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ number(summary.total_quantity_kg) }} kg</h6>
                  <span class="text-muted small">Quantity harvested</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Labour Expenses</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6>KSh {{ money(summary.total_labour_cost) }}</h6>
                  <span class="text-muted small">Labour</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Transport Expenses</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-truck"></i>
                </div>
                <div class="ps-3">
                  <h6>KSh {{ money(summary.total_transport_cost) }}</h6>
                  <span class="text-muted small">Transportation</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Total Harvest Cost</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cash-stack"></i>
                </div>
                <div class="ps-3">
                  <h6>KSh {{ money(summary.total_cost) }}</h6>
                  <span class="text-muted small">
                    KSh {{ money(summary.cost_per_kg) }} per kg
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Production graph -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <h5 class="card-title mb-1">Harvest Trend</h5>
              <p class="text-muted small">
                Kilograms recorded per harvest date for the selected filters.
              </p>
            </div>
            <span class="badge bg-light text-dark">
              {{ chartRecords.length }} harvest records
            </span>
          </div>

          <div v-if="initializing" class="text-center py-5">
            <div class="spinner-border text-success"></div>
          </div>

          <div v-else-if="!chartRecords.length"
               class="text-center text-muted py-5">
            No harvest data available for the selected filters.
          </div>

          <div v-else class="harvest-chart">
            <svg viewBox="0 0 760 270"
                 preserveAspectRatio="xMidYMid meet"
                 role="img"
                 aria-label="Crop harvest quantities by date">
              <g v-for="tick in chartTicks" :key="tick">
                <line x1="65" :y1="chartY(tick)"
                      x2="745" :y2="chartY(tick)"
                      stroke="currentColor" stroke-opacity=".12"/>
                <text x="55" :y="chartY(tick) + 4"
                      text-anchor="end" font-size="11"
                      fill="currentColor">
                  {{ number(tick) }}
                </text>
              </g>

              <g v-for="(item, index) in chartRecords" :key="item.id">
                <rect
                  :x="chartX(index)"
                  :y="chartY(Number(item.quantity_kg))"
                  :width="chartBarWidth"
                  :height="chartBarHeight(item.quantity_kg)"
                  rx="3"
                  fill="#176b38">
                  <title>
                    {{ item.harvest_date }} — {{ item.season || 'No season' }}:
                    {{ item.quantity_kg }} kg
                  </title>
                </rect>

                <text
                  v-if="chartRecords.length <= 14"
                  :x="chartX(index) + chartBarWidth / 2"
                  :y="chartY(Number(item.quantity_kg)) - 7"
                  text-anchor="middle"
                  font-size="10"
                  fill="currentColor">
                  {{ number(item.quantity_kg) }}
                </text>

                <text
                  :x="chartX(index) + chartBarWidth / 2"
                  y="246"
                  text-anchor="end"
                  font-size="10"
                  fill="currentColor"
                  :transform="`rotate(-35 ${chartX(index) + chartBarWidth / 2} 246)`">
                  {{ shortDate(item.harvest_date) }}
                </text>
              </g>
            </svg>
            <div class="text-muted small text-center">
              Harvest date · Quantity in kilograms
            </div>
          </div>
        </div>
      </div>

      <!-- Harvest register -->
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title">
              Harvest Register
              <span class="text-muted">| Crop production records</span>
            </h5>
            <button class="btn btn-sm btn-outline-success"
                    @click="exportCsv"
                    :disabled="!harvests.length">
              <i class="bi bi-download me-1"></i> Export CSV
            </button>
          </div>

          <div v-if="initializing" class="text-center py-5">
            <div class="spinner-border text-success"></div>
            <p class="text-muted mt-2">Loading harvest records...</p>
          </div>

          <div v-else-if="!harvests.length"
               class="text-center text-muted py-5">
            <i class="bi bi-basket display-5"></i>
            <p class="mt-2">No harvest records found.</p>
            <button class="btn btn-success"
                    style="background: darkgreen; border-color: darkgreen"
                    @click="openAddModal">
              Record your first harvest
            </button>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Harvest Date</th>
                  <th>Crop / Variety</th>
                  <th>Season</th>
                  <th>Quantity (kg)</th>
                  <th>Labour</th>
                  <th>Transport</th>
                  <th>Total Cost</th>
                  <th>Cost / kg</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in harvests" :key="item.id">
                  <td class="text-nowrap">{{ item.harvest_date }}</td>
                  <td>
                    <div>{{ item.crop?.crop_name || 'Crop' }}</div>
                    <small class="text-muted">
                      {{ item.crop?.variety || '—' }}
                    </small>
                  </td>
                  <td class="text-nowrap">
                    <span v-if="item.season"
                          class="badge bg-light text-dark border">
                      {{ item.season }}
                    </span>
                    <span v-else class="text-muted">—</span>
                  </td>
                  <td class="fw-semibold">{{ number(item.quantity_kg) }}</td>
                  <td>KSh {{ money(item.labour_cost) }}</td>
                  <td>KSh {{ money(item.transport_cost) }}</td>
                  <td class="fw-semibold">KSh {{ money(item.total_cost) }}</td>
                  <td>
                    {{ item.cost_per_kg == null
                      ? '—'
                      : 'KSh ' + money(item.cost_per_kg) }}
                  </td>
                  <td class="description-cell">
                    {{ item.description || '—' }}
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-success dropdown-toggle"
                              data-bs-toggle="dropdown">
                        Action
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <button class="dropdown-item"
                                  @click="viewHarvest(item)">
                            <i class="bi bi-eye me-2"></i>View
                          </button>
                        </li>
                        <li>
                          <button class="dropdown-item"
                                  @click="openEditModal(item)">
                            <i class="bi bi-pencil me-2"></i>Edit
                          </button>
                        </li>
                        <li>
                          <button
                            class="dropdown-item"
                            @click="openFundingModal(item)"
                          >
                            <i class="bi bi-wallet2 me-2"></i>
                            Funding & Reimbursements
                          </button>
                        </li>
                        <li>
                          <button class="dropdown-item text-danger"
                                  @click="deleteHarvest(item)">
                            <i class="bi bi-trash me-2"></i>Delete
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1"
                 class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
              <small class="text-muted">
                Showing {{ pagination.from || 0 }}–{{ pagination.to || 0 }}
                of {{ pagination.total }} records
              </small>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary"
                        :disabled="pagination.current_page <= 1"
                        @click="changePage(pagination.current_page - 1)">
                  Previous
                </button>
                <button class="btn btn-sm btn-outline-secondary" disabled>
                  {{ pagination.current_page }} / {{ pagination.last_page }}
                </button>
                <button class="btn btn-sm btn-outline-secondary"
                        :disabled="pagination.current_page >= pagination.last_page"
                        @click="changePage(pagination.current_page + 1)">
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add / Edit Modal -->
      <div class="modal fade" id="cropHarvestModal"
           tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                {{ editing ? 'Edit Harvest Record' : 'Record Crop Harvest' }}
              </h5>
              <button type="button" class="btn-close"
                      data-bs-dismiss="modal"></button>
            </div>

            <form @submit.prevent="saveHarvest">
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Crop *</label>
                    <select class="form-select"
                            v-model="form.crop_id"
                            @change="onCropOrDateChanged"
                            required>
                      <option value="">Select crop</option>
                      <option v-for="crop in crops" :key="crop.id"
                              :value="String(crop.id)">
                        {{ crop.crop_name }}
                        {{ crop.variety ? ` - ${crop.variety}` : '' }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Harvest Date *</label>
                    <input type="date" class="form-control"
                           v-model="form.harvest_date"
                           @change="onCropOrDateChanged"
                           required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">
                      Season *
                      <small class="text-muted">(Editable)</small>
                    </label>
                    <input type="text"
                           class="form-control"
                           v-model="form.season"
                           @input="onSeasonInput"
                           maxlength="100"
                           placeholder="e.g. 2026 Long Rains"
                           required>
                    <small class="text-muted">
                      The suggested season can be changed to match this crop's
                      actual growing season.
                    </small>
                    <div v-if="seasonSuggestion"
                         class="mt-2 small">
                      Suggested season: <strong>{{ seasonSuggestion }}</strong>
                      <button type="button"
                              class="btn btn-sm btn-link p-0 ms-2"
                              @click="useSuggestedSeason">
                        Use suggestion
                      </button>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Quantity Harvested (kg) *</label>
                    <input type="number" class="form-control"
                           v-model.number="form.quantity_kg"
                           min="0.01" step="0.01" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Labour Cost (KSh) *</label>
                    <input type="number" class="form-control"
                           v-model.number="form.labour_cost"
                           min="0" step="0.01" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Transport Cost (KSh) *</label>
                    <input type="number" class="form-control"
                           v-model.number="form.transport_cost"
                           min="0" step="0.01" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Other Expenses (KSh)</label>
                    <input type="number" class="form-control"
                           v-model.number="form.other_cost"
                           min="0" step="0.01">
                  </div>

                  <div class="col-12">
                    <label class="form-label">Description / Notes</label>
                    <textarea class="form-control" rows="3"
                              v-model="form.description"
                              placeholder="Harvest quality, weather, labour details, delivery notes..."></textarea>
                  </div>

                  <div class="col-12">
                    <div class="rounded p-3 bg-light">
                      <div class="d-flex justify-content-between">
                        <span>Total harvesting expenses</span>
                        <strong>KSh {{ money(formTotalCost) }}</strong>
                      </div>
                      <div class="d-flex justify-content-between mt-1">
                        <span>Cost per kilogram</span>
                        <strong>
                          {{ Number(form.quantity_kg) > 0
                            ? 'KSh ' + money(formTotalCost / Number(form.quantity_kg))
                            : '—' }}
                        </strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-success"
                        style="background: darkgreen; border-color: darkgreen"
                        :disabled="submitting">
                  <span v-if="submitting"
                        class="spinner-border spinner-border-sm me-1"></span>
                  {{ submitting ? 'Saving...' : 'Save Harvest' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- View Modal -->
      <div class="modal fade" id="viewCropHarvestModal"
           tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Harvest Details</h5>
              <button type="button" class="btn-close"
                      data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" v-if="selectedHarvest">
              <div class="row g-3">
                <div class="col-md-6">
                  <strong>Crop:</strong>
                  <div>{{ selectedHarvest.crop?.crop_name || 'Crop' }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Variety:</strong>
                  <div>{{ selectedHarvest.crop?.variety || '—' }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Harvest Date:</strong>
                  <div>{{ selectedHarvest.harvest_date }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Season:</strong>
                  <div>{{ selectedHarvest.season || '—' }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Quantity:</strong>
                  <div>{{ number(selectedHarvest.quantity_kg) }} kg</div>
                </div>
                <div class="col-md-6">
                  <strong>Labour:</strong>
                  <div>KSh {{ money(selectedHarvest.labour_cost) }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Transport:</strong>
                  <div>KSh {{ money(selectedHarvest.transport_cost) }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Other expenses:</strong>
                  <div>KSh {{ money(selectedHarvest.other_cost) }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Total cost:</strong>
                  <div>KSh {{ money(selectedHarvest.total_cost) }}</div>
                </div>
                <div class="col-md-6">
                  <strong>Cost per kg:</strong>
                  <div>
                    {{ selectedHarvest.cost_per_kg == null
                      ? '—'
                      : 'KSh ' + money(selectedHarvest.cost_per_kg) }}
                  </div>
                </div>
                <div class="col-12">
                  <strong>Description:</strong>
                  <p class="mb-0 mt-1">
                    {{ selectedHarvest.description || 'No notes recorded.' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Funding & Reimbursements Modal -->
      <div
        class="modal fade"
        id="cropHarvestFundingModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <div>
                <h5 class="modal-title">Funding & Reimbursements</h5>
                <small class="text-muted" v-if="fundingHarvest">
                  {{ fundingHarvest.crop?.crop_name || 'Crop' }}
                  · {{ fundingHarvest.harvest_date }}
                  · Season {{ fundingHarvest.season || '—' }}
                </small>
              </div>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
              ></button>
            </div>

            <div class="modal-body">
              <div v-if="fundingLoading" class="text-center py-5">
                <div class="spinner-border text-success"></div>
                <p class="text-muted mt-2">Loading funding ledger...</p>
              </div>

              <template v-else-if="fundingHarvest">
                <!-- Summary -->
                <div class="row g-3 mb-4">
                  <div class="col-6 col-lg-3">
                    <div class="card h-100 border">
                      <div class="card-body">
                        <small class="text-muted">Harvest expenses</small>
                        <h5 class="mt-2 mb-0">
                          KSh {{ money(fundingLedger.total_expenses) }}
                        </h5>
                      </div>
                    </div>
                  </div>

                  <div class="col-6 col-lg-3">
                    <div class="card h-100 border">
                      <div class="card-body">
                        <small class="text-muted">Total funded</small>
                        <h5 class="mt-2 mb-0 text-success">
                          KSh {{ money(fundingLedger.total_funded) }}
                        </h5>
                      </div>
                    </div>
                  </div>

                  <div class="col-6 col-lg-3">
                    <div class="card h-100 border">
                      <div class="card-body">
                        <small class="text-muted">Reimbursed</small>
                        <h5 class="mt-2 mb-0">
                          KSh {{ money(fundingLedger.total_repaid) }}
                        </h5>
                      </div>
                    </div>
                  </div>

                  <div class="col-6 col-lg-3">
                    <div class="card h-100 border">
                      <div class="card-body">
                        <small class="text-muted">Outstanding</small>
                        <h5 class="mt-2 mb-0 text-danger">
                          KSh {{ money(fundingLedger.total_outstanding) }}
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>

                <div
                  v-if="Number(fundingLedger.unfunded_expenses) > 0"
                  class="alert alert-warning"
                >
                  <i class="bi bi-exclamation-triangle me-2"></i>
                  KSh {{ money(fundingLedger.unfunded_expenses) }}
                  of this harvest's expenses have not yet been assigned a funding source.
                </div>

                <!-- Funding source balances -->
                <h6 class="fw-bold mb-3">Balances by Funding Source</h6>

                <div
                  v-if="!fundingLedger.balances?.length"
                  class="text-muted border rounded p-3 mb-4"
                >
                  No funding transactions recorded yet.
                </div>

                <div v-else class="table-responsive mb-4">
                  <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Source</th>
                        <th class="text-end">Advanced</th>
                        <th class="text-end">Repaid</th>
                        <th class="text-end">Outstanding</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="balance in fundingLedger.balances"
                        :key="balance.funding_source"
                      >
                        <td class="text-capitalize">
                          {{ fundingSourceLabel(balance.funding_source) }}
                        </td>
                        <td class="text-end">
                          KSh {{ money(balance.total_funded) }}
                        </td>
                        <td class="text-end">
                          KSh {{ money(balance.total_repaid) }}
                        </td>
                        <td class="text-end fw-semibold">
                          KSh {{ money(balance.outstanding) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Add / edit transaction -->
                <div class="card border mb-4">
                  <div class="card-body">
                    <h6 class="card-title">
                      {{ editingFunding
                        ? 'Edit Transaction'
                        : 'Record a Transaction' }}
                    </h6>

                    <form @submit.prevent="saveFundingTransaction">
                      <div class="row g-3">
                        <div class="col-md-4">
                          <label class="form-label">Transaction Type *</label>
                          <select
                            class="form-select"
                            v-model="fundingForm.transaction_type"
                            :disabled="editingFunding"
                            required
                          >
                            <option value="funded">Funding / Advance</option>
                            <option value="repaid">Reimbursement / Repayment</option>
                          </select>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label">Funding Source *</label>
                          <input
                            class="form-control"
                            list="harvestFundingSources"
                            v-model.trim="fundingForm.funding_source"
                            maxlength="30"
                            placeholder="e.g. shop, mum, personal"
                            required
                          >

                          <datalist id="harvestFundingSources">
                            <option value="shop">Cyber / Shop</option>
                            <option value="mum">Mum</option>
                            <option value="personal">Personal</option>
                            <option value="other">Other</option>
                          </datalist>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label">Amount (KSh) *</label>
                          <input
                            type="number"
                            class="form-control"
                            v-model.number="fundingForm.amount"
                            min="0.01"
                            step="0.01"
                            required
                          >
                        </div>

                        <div class="col-md-4">
                          <label class="form-label">Transaction Date *</label>
                          <input
                            type="date"
                            class="form-control"
                            v-model="fundingForm.transaction_date"
                            required
                          >
                        </div>

                        <div class="col-md-8">
                          <label class="form-label">Notes</label>
                          <input
                            type="text"
                            class="form-control"
                            v-model.trim="fundingForm.notes"
                            maxlength="5000"
                            placeholder="What was this transaction for?"
                          >
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2">
                          <button
                            type="submit"
                            class="btn btn-success"
                            style="background: darkgreen; border-color: darkgreen"
                            :disabled="fundingSubmitting"
                          >
                            <span
                              v-if="fundingSubmitting"
                              class="spinner-border spinner-border-sm me-1"
                            ></span>

                            {{
                              fundingSubmitting
                                ? 'Saving...'
                                : editingFunding
                                  ? 'Update Transaction'
                                  : 'Save Transaction'
                            }}
                          </button>

                          <button
                            v-if="editingFunding"
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetFundingForm"
                          >
                            Cancel Edit
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

                <!-- Transaction history -->
                <h6 class="fw-bold mb-3">Transaction History</h6>

                <div
                  v-if="!fundingLedger.transactions?.length"
                  class="text-center text-muted py-4 border rounded"
                >
                  No funding or reimbursement transactions recorded.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-hover align-middle">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th>Notes</th>
                        <th>Actions</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr
                        v-for="transaction in fundingLedger.transactions"
                        :key="transaction.id"
                      >
                        <td class="text-nowrap">
                          {{ transaction.transaction_date }}
                        </td>

                        <td class="text-capitalize">
                          {{ fundingSourceLabel(transaction.funding_source) }}
                        </td>

                        <td>
                          <span
                            :class="transaction.transaction_type === 'funded'
                              ? 'badge bg-success'
                              : 'badge bg-primary'"
                          >
                            {{ transaction.transaction_type === 'funded'
                              ? 'Funding'
                              : 'Reimbursement' }}
                          </span>
                        </td>

                        <td class="text-end text-nowrap">
                          KSh {{ money(transaction.amount) }}
                        </td>

                        <td class="text-break">
                          {{ transaction.notes || '—' }}
                        </td>

                        <td>
                          <div class="btn-group btn-group-sm">
                            <button
                              type="button"
                              class="btn btn-outline-success"
                              title="Edit transaction"
                              @click="editFundingTransaction(transaction)"
                            >
                              <i class="bi bi-pencil"></i>
                            </button>

                            <button
                              type="button"
                              class="btn btn-outline-danger"
                              title="Delete transaction"
                              @click="deleteFundingTransaction(transaction)"
                            >
                              <i class="bi bi-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </template>
            </div>

            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>      

    </section>
  </Master>
</template>

<style scoped>
.harvest-chart {
  width: 100%;
  overflow-x: auto;
}

.harvest-chart svg {
  width: 100%;
  min-width: 600px;
  height: auto;
  display: block;
}

.description-cell {
  max-width: 220px;
  white-space: normal;
}

.card-icon {
  width: 46px;
  height: 46px;
  background: rgba(25, 135, 84, 0.10);
  color: darkgreen;
  font-size: 23px;
}

.table th {
  white-space: nowrap;
}

@media (max-width: 576px) {
  .harvest-chart svg {
    min-width: 650px;
  }
}
</style>

<script>
import Master from "@/components/Master.vue";
import axios from "axios";
import Swal from "sweetalert2";

const toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
});

const emptyForm = () => ({
  crop_id: "",
  harvest_date: new Date().toISOString().slice(0, 10),
  season: "",
  quantity_kg: "",
  labour_cost: 0,
  transport_cost: 0,
  other_cost: 0,
  description: "Routine crop harvesting and collection.",
});

export default {
  name: "CropHarvest",

  components: {
    Master,
  },

  data() {
    return {
      crops: [],
      harvests: [],
      selectedHarvest: null,

      initializing: true,
      submitting: false,
      editing: false,

      // True when the user has deliberately edited the season.
      seasonManuallySet: false,
      seasonSuggestion: "",

      filters: {
        crop_id: "",
        season: "",
        period: "all",
        date_from: "",
        date_to: "",
      },

      summary: {
        harvest_count: 0,
        total_quantity_kg: 0,
        total_labour_cost: 0,
        total_transport_cost: 0,
        total_other_cost: 0,
        total_cost: 0,
        cost_per_kg: null,
      },

      fundingHarvest: null,
      fundingLoading: false,
      fundingSubmitting: false,
      editingFunding: false,

      fundingForm: {
        id: null,
        funding_source: "",
        transaction_type: "funded",
        amount: "",
        transaction_date: new Date().toISOString().slice(0, 10),
        notes: "",
      },

      fundingLedger: {
        total_expenses: 0,
        total_funded: 0,
        total_repaid: 0,
        total_outstanding: 0,
        unfunded_expenses: 0,
        balances: [],
        transactions: [],
      },      

      form: emptyForm(),

      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 100,
        total: 0,
        from: 0,
        to: 0,
      },

      requestError: null,
    };
  },

  computed: {
    formTotalCost() {
      return (
        (Number(this.form.labour_cost) || 0) +
        (Number(this.form.transport_cost) || 0) +
        (Number(this.form.other_cost) || 0)
      );
    },

    // Season options found in the currently loaded harvest records.
    availableSeasons() {
      const seasons = this.harvests
        .map(item => String(item.season || "").trim())
        .filter(Boolean);

      // Preserve a currently selected season even if it isn't in this page.
      if (this.filters.season) {
        seasons.push(this.filters.season);
      }

      return [...new Set(seasons)].sort((a, b) =>
        a.localeCompare(b)
      );
    },

    chartRecords() {
      return [...this.harvests].sort((a, b) =>
        String(a.harvest_date).localeCompare(String(b.harvest_date))
      );
    },

    chartMaximum() {
      const highest = Math.max(
        0,
        ...this.chartRecords.map(item => Number(item.quantity_kg) || 0)
      );

      if (highest === 0) return 10;

      return Math.ceil(highest / 10) * 10 || highest;
    },

    chartTicks() {
      const maximum = this.chartMaximum;
      return [
        maximum,
        maximum * 0.75,
        maximum * 0.5,
        maximum * 0.25,
        0,
      ];
    },

    chartBarWidth() {
      const count = this.chartRecords.length;
      if (!count) return 0;

      return Math.min(32, (680 / count) * 0.62);
    },
  },

  methods: {
    money(value) {
      if (value === null || value === undefined || value === "") {
        return "0.00";
      }

      return (Number(value) || 0).toLocaleString("en-KE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    number(value) {
      return (Number(value) || 0).toLocaleString("en-KE", {
        maximumFractionDigits: 2,
      });
    },

    shortDate(date) {
      if (!date) return "";

      const parts = String(date).slice(0, 10).split("-");
      if (parts.length !== 3) return date;

      return `${parts[2]}/${parts[1]}`;
    },

    chartX(index) {
      const count = this.chartRecords.length;
      const slotWidth = count ? 680 / count : 0;

      return 65 + slotWidth * index +
        (slotWidth - this.chartBarWidth) / 2;
    },

    chartY(value) {
      const chartTop = 20;
      const chartBottom = 220;
      const maximum = this.chartMaximum;

      return chartBottom -
        ((Number(value) || 0) / maximum) * (chartBottom - chartTop);
    },

    chartBarHeight(value) {
      return Math.max(0, 220 - this.chartY(value));
    },

    dateString(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },

    applyPeriod() {
      const today = new Date();
      const todayString = this.dateString(today);

      if (this.filters.period === "all") {
        this.filters.date_from = "";
        this.filters.date_to = "";
      } else if (this.filters.period === "today") {
        this.filters.date_from = todayString;
        this.filters.date_to = todayString;
      } else if (this.filters.period === "week") {
        const start = new Date(today);
        const day = (start.getDay() + 6) % 7;

        start.setDate(start.getDate() - day);

        this.filters.date_from = this.dateString(start);
        this.filters.date_to = todayString;
      } else if (this.filters.period === "month") {
        this.filters.date_from = this.dateString(
          new Date(today.getFullYear(), today.getMonth(), 1)
        );
        this.filters.date_to = todayString;
      } else if (this.filters.period === "year") {
        this.filters.date_from = `${today.getFullYear()}-01-01`;
        this.filters.date_to = todayString;
      }

      // Custom dates remain manually entered.
      this.pagination.current_page = 1;
      this.loadLists();
    },

    applyFilters() {
      this.pagination.current_page = 1;
      this.loadLists();
    },

    resetFilters() {
      this.filters = {
        crop_id: "",
        season: "",
        period: "all",
        date_from: "",
        date_to: "",
      };

      this.pagination.current_page = 1;
      this.loadLists();
    },

    filterParams() {
      const params = {
        per_page: 100,
        page: this.pagination.current_page,
      };

      if (this.filters.crop_id) {
        params.crop_id = this.filters.crop_id;
      }

      if (this.filters.season) {
        params.season = this.filters.season;
      }

      if (this.filters.date_from) {
        params.date_from = this.filters.date_from;
      }

      if (this.filters.date_to) {
        params.date_to = this.filters.date_to;
      }

      return params;
    },

    async loadCrops() {
      try {
        const response = await axios.get("/api/crops");
        const payload = response.data;

        const list = Array.isArray(payload)
          ? payload
          : payload.crops ??
            payload.data?.data ??
            payload.data ??
            [];

        this.crops = Array.isArray(list) ? list : [];
      } catch (error) {
        console.error("Failed to load crops:", error);
        this.crops = [];

        toast.fire({
          icon: "warning",
          title: "Could not load crops",
          text: "Check your crops API endpoint.",
        });
      }
    },

    async loadLists() {
      this.initializing = true;
      this.requestError = null;

      try {
        const params = this.filterParams();

        const summaryParams = {
          crop_id: this.filters.crop_id || undefined,
          season: this.filters.season || undefined,
          date_from: this.filters.date_from || undefined,
          date_to: this.filters.date_to || undefined,
        };

        const [listResponse, summaryResponse] = await Promise.all([
          axios.get("/api/crop-harvests", { params }),
          axios.get("/api/crop-harvests/summary", {
            params: summaryParams,
          }),
        ]);

        const result = listResponse.data.data;

        if (Array.isArray(result)) {
          this.harvests = result;
          this.pagination = {
            current_page: 1,
            last_page: 1,
            per_page: result.length,
            total: result.length,
            from: result.length ? 1 : 0,
            to: result.length,
          };
        } else {
          this.harvests = result?.data ?? [];

          this.pagination = {
            current_page: result?.current_page ?? 1,
            last_page: result?.last_page ?? 1,
            per_page: result?.per_page ?? 100,
            total: result?.total ?? this.harvests.length,
            from: result?.from ?? 0,
            to: result?.to ?? this.harvests.length,
          };
        }

        this.summary = summaryResponse.data.data ?? this.summary;
      } catch (error) {
        console.error("Failed to load crop harvest records:", error);

        this.requestError = error.response?.data?.message ??
          "Unable to load harvest records. Check your API routes and server logs.";

        toast.fire({
          icon: "error",
          title: "Failed to load harvest records",
          text: this.requestError,
        });
      } finally {
        this.initializing = false;
      }
    },

    async changePage(page) {
      if (page < 1 || page > this.pagination.last_page) return;

      this.pagination.current_page = page;
      await this.loadLists();
    },

    /*
     * Season suggestion:
     * 1. Prefer the same crop and calendar year.
     * 2. Otherwise use the most recent season recorded for that crop.
     * 3. If no matching history exists, leave the season blank.
     *
     * This is a historical suggestion, not an agronomic calendar.
     */
findSeasonSuggestion(cropId, harvestDate) {
  if (!harvestDate) return "";

  const date = new Date(`${harvestDate}T00:00:00`);
  const year = date.getFullYear();
  const month = date.getMonth() + 1;

  // January–June: previous year/current year
  // July–December: current year/next year
  const suggestedSeason = month <= 6
    ? `${year - 1}/${year}`
    : `${year}/${year + 1}`;

  if (!cropId) return suggestedSeason;

  const cropRecords = this.harvests
    .filter(item =>
      Number(item.crop_id) === Number(cropId) &&
      String(item.season || "").trim() !== "" &&
      item.harvest_date
    )
    .sort((a, b) =>
      String(b.harvest_date).localeCompare(String(a.harvest_date))
    );

  const sameYear = cropRecords.find(item =>
    String(item.harvest_date).slice(0, 4) === String(year)
  );

  return String(sameYear?.season || suggestedSeason).trim();
},

    onCropOrDateChanged() {
      if (this.editing || this.seasonManuallySet) return;

      const suggestion = this.findSeasonSuggestion(
        this.form.crop_id,
        this.form.harvest_date
      );

      this.seasonSuggestion = suggestion;
      this.form.season = suggestion;
    },

    onSeasonInput() {
      this.seasonManuallySet = true;
    },

    useSuggestedSeason() {
      this.form.season = this.seasonSuggestion;
      this.seasonManuallySet = false;
    },

openAddModal() {
  this.editing = false;
  this.seasonManuallySet = false;
  this.form = emptyForm();

  this.seasonSuggestion = this.findSeasonSuggestion(
    this.form.crop_id,
    this.form.harvest_date
  );

  this.form.season = this.seasonSuggestion;

  this.showModal("cropHarvestModal");
},

    openEditModal(item) {
      this.editing = true;
      this.seasonManuallySet = true;
      this.seasonSuggestion = "";

      this.form = {
        id: item.id,
        crop_id: String(item.crop_id),
        harvest_date: String(item.harvest_date).slice(0, 10),
        season: item.season || "",
        quantity_kg: Number(item.quantity_kg),
        labour_cost: Number(item.labour_cost),
        transport_cost: Number(item.transport_cost),
        other_cost: Number(item.other_cost || 0),
        description: item.description || "",
      };

      this.showModal("cropHarvestModal");
    },

    viewHarvest(item) {
      this.selectedHarvest = item;
      this.showModal("viewCropHarvestModal");
    },

    showModal(id) {
      const element = document.getElementById(id);

      if (element && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(element).show();
      } else {
        toast.fire({
          icon: "error",
          title: "Bootstrap modal is unavailable",
        });
      }
    },

    hideModal(id) {
      const element = document.getElementById(id);

      if (element && window.bootstrap) {
        window.bootstrap.Modal.getInstance(element)?.hide();
      }
    },

    async saveHarvest() {
      if (this.submitting) return;

      if (!this.form.crop_id || !this.form.harvest_date) {
        toast.fire({
          icon: "warning",
          title: "Select a crop and harvest date",
        });
        return;
      }

      if (!String(this.form.season || "").trim()) {
        toast.fire({
          icon: "warning",
          title: "Enter the harvest season",
        });
        return;
      }

      if (
        !Number.isFinite(Number(this.form.quantity_kg)) ||
        Number(this.form.quantity_kg) <= 0
      ) {
        toast.fire({
          icon: "warning",
          title: "Enter a valid harvested quantity",
        });
        return;
      }

      const payload = {
        crop_id: Number(this.form.crop_id),
        harvest_date: this.form.harvest_date,
        season: String(this.form.season).trim(),
        quantity_kg: Number(this.form.quantity_kg),
        labour_cost: Number(this.form.labour_cost || 0),
        transport_cost: Number(this.form.transport_cost || 0),
        other_cost: Number(this.form.other_cost || 0),
        description: this.form.description || null,
      };

      this.submitting = true;

      try {
        if (this.editing) {
          await axios.put(
            `/api/crop-harvests/${this.form.id}`,
            payload
          );

          toast.fire({
            icon: "success",
            title: "Harvest record updated",
          });
        } else {
          await axios.post("/api/crop-harvests", payload);

          toast.fire({
            icon: "success",
            title: "Harvest recorded successfully",
          });
        }

        this.hideModal("cropHarvestModal");
        await this.loadLists();
      } catch (error) {
        console.error("Failed to save harvest:", error);

        const errors = error.response?.data?.errors;
        const firstValidationError = errors
          ? Object.values(errors).flat()[0]
          : null;

        toast.fire({
          icon: "error",
          title: "Could not save harvest",
          text: firstValidationError ??
            error.response?.data?.message ??
            "Please check the information and try again.",
        });
      } finally {
        this.submitting = false;
      }
    },

    async deleteHarvest(item) {
      const result = await Swal.fire({
        title: "Delete harvest record?",
        text: `This will delete the harvest recorded on ${item.harvest_date}.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#006400",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
      });

      if (!result.isConfirmed) return;

      try {
        await axios.delete(`/api/crop-harvests/${item.id}`);

        toast.fire({
          icon: "success",
          title: "Harvest record deleted",
        });

        if (
          this.harvests.length === 1 &&
          this.pagination.current_page > 1
        ) {
          this.pagination.current_page--;
        }

        await this.loadLists();
      } catch (error) {
        console.error("Failed to delete harvest:", error);

        toast.fire({
          icon: "error",
          title: "Delete failed",
          text: error.response?.data?.message ??
            "Could not delete the harvest record.",
        });
      }
    },

    fundingSourceLabel(source) {
      const labels = {
        shop: "Cyber / Shop",
        mum: "Mum",
        personal: "Personal",
        other: "Other",
      };

      const key = String(source || "").trim().toLowerCase();

      return labels[key] || String(source || "Unknown");
    },

    emptyFundingLedger() {
      return {
        total_expenses: 0,
        total_funded: 0,
        total_repaid: 0,
        total_outstanding: 0,
        unfunded_expenses: 0,
        balances: [],
        transactions: [],
      };
    },

    resetFundingForm() {
      this.editingFunding = false;

      this.fundingForm = {
        id: null,
        funding_source: "",
        transaction_type: "funded",
        amount: "",
        transaction_date: new Date().toISOString().slice(0, 10),
        notes: "",
      };
    },

    async openFundingModal(item) {
      this.fundingHarvest = item;
      this.fundingLedger = this.emptyFundingLedger();
      this.resetFundingForm();

      this.showModal("cropHarvestFundingModal");

      await this.loadFundingLedger();
    },

    async loadFundingLedger() {
      if (!this.fundingHarvest?.id) return;

      this.fundingLoading = true;

      try {
        const response = await axios.get(
          `/api/crop-harvests/${this.fundingHarvest.id}/fundings`
        );

        const result = response.data?.data;

        if (!result) {
          throw new Error("The funding API returned an invalid response.");
        }

        this.fundingLedger = {
          ...this.emptyFundingLedger(),
          ...result,
          balances: Array.isArray(result.balances)
            ? result.balances
            : [],
          transactions: Array.isArray(result.transactions)
            ? result.transactions
            : [],
        };
      } catch (error) {
        console.error("Failed to load harvest funding:", error);

        toast.fire({
          icon: "error",
          title: "Could not load funding ledger",
          text: error.response?.data?.message ||
            error.message ||
            "Please check the funding API.",
        });
      } finally {
        this.fundingLoading = false;
      }
    },

    editFundingTransaction(transaction) {
      this.editingFunding = true;

      this.fundingForm = {
        id: transaction.id,
        funding_source: transaction.funding_source || "",
        transaction_type: transaction.transaction_type,
        amount: Number(transaction.amount),
        transaction_date: String(
          transaction.transaction_date
        ).slice(0, 10),
        notes: transaction.notes || "",
      };

      // Keep the transaction form visible when editing.
      document
        .getElementById("cropHarvestFundingModal")
        ?.querySelector(".modal-body")
        ?.scrollTo({
          top: 0,
          behavior: "smooth",
        });
    },

    async saveFundingTransaction() {
      if (this.fundingSubmitting || !this.fundingHarvest?.id) return;

      const source = String(
        this.fundingForm.funding_source || ""
      ).trim().toLowerCase();

      const amount = Number(this.fundingForm.amount);

      if (!source) {
        toast.fire({
          icon: "warning",
          title: "Enter a funding source",
        });
        return;
      }

      if (!Number.isFinite(amount) || amount <= 0) {
        toast.fire({
          icon: "warning",
          title: "Enter an amount greater than zero",
        });
        return;
      }

      if (!this.fundingForm.transaction_date) {
        toast.fire({
          icon: "warning",
          title: "Select a transaction date",
        });
        return;
      }

      const payload = {
        funding_source: source,
        amount,
        transaction_date: this.fundingForm.transaction_date,
        notes: this.fundingForm.notes || null,
      };

      this.fundingSubmitting = true;

      try {
        if (this.editingFunding) {
          // Requires PUT /api/crop-harvest-fundings/{id}
          await axios.put(
            `/api/crop-harvest-fundings/${this.fundingForm.id}`,
            payload
          );

          toast.fire({
            icon: "success",
            title: "Transaction updated",
          });
        } else if (this.fundingForm.transaction_type === "repaid") {
          await axios.post(
            `/api/crop-harvests/${this.fundingHarvest.id}/fundings/repay`,
            payload
          );

          toast.fire({
            icon: "success",
            title: "Reimbursement recorded",
          });
        } else {
          await axios.post(
            `/api/crop-harvests/${this.fundingHarvest.id}/fundings`,
            payload
          );

          toast.fire({
            icon: "success",
            title: "Funding recorded",
          });
        }

        this.resetFundingForm();

        await this.loadFundingLedger();
      } catch (error) {
        console.error("Failed to save funding transaction:", error);

        const errors = error.response?.data?.errors;
        const firstValidationError = errors
          ? Object.values(errors).flat()[0]
          : null;

        toast.fire({
          icon: "error",
          title: "Transaction could not be saved",
          text: firstValidationError ||
            error.response?.data?.message ||
            "Check the API route and try again.",
        });
      } finally {
        this.fundingSubmitting = false;
      }
    },

    async deleteFundingTransaction(transaction) {
      const result = await Swal.fire({
        title: "Delete this transaction?",
        text: `This will remove the ${transaction.transaction_type === "funded"
          ? "funding entry"
          : "reimbursement entry"} of KSh ${this.money(transaction.amount)}.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
      });

      if (!result.isConfirmed) return;

      try {
        // Requires DELETE /api/crop-harvest-fundings/{id}
        await axios.delete(
          `/api/crop-harvest-fundings/${transaction.id}`
        );

        if (this.fundingForm.id === transaction.id) {
          this.resetFundingForm();
        }

        toast.fire({
          icon: "success",
          title: "Transaction deleted",
        });

        await this.loadFundingLedger();
      } catch (error) {
        console.error("Failed to delete funding transaction:", error);

        toast.fire({
          icon: "error",
          title: "Delete failed",
          text: error.response?.data?.message ||
            "Could not delete the transaction.",
        });
      }
    },    

    exportCsv() {
      if (!this.harvests.length) return;

      const columns = [
        ["Harvest Date", "harvest_date"],
        ["Crop", "crop_name"],
        ["Variety", "variety"],
        ["Season", "season"],
        ["Quantity (kg)", "quantity_kg"],
        ["Labour Cost", "labour_cost"],
        ["Transport Cost", "transport_cost"],
        ["Other Cost", "other_cost"],
        ["Total Cost", "total_cost"],
        ["Cost Per Kg", "cost_per_kg"],
        ["Description", "description"],
      ];

      const escapeCsv = value => {
        let text = String(value ?? "");

        // Prevent spreadsheet formula injection from user-entered text.
        if (/^[\s]*[=+\-@]/.test(text)) {
          text = "'" + text;
        }

        return `"${text.replace(/"/g, '""')}"`;
      };

      const rows = [
        columns.map(column => escapeCsv(column[0])).join(","),
        ...this.harvests.map(item => {
          const row = {
            ...item,
            crop_name: item.crop?.crop_name || "Crop",
            variety: item.crop?.variety || "",
            season: item.season || "",
          };

          return columns
            .map(([, key]) => escapeCsv(row[key]))
            .join(",");
        }),
      ];

      const blob = new Blob(
        ["\uFEFF" + rows.join("\r\n")],
        { type: "text/csv;charset=utf-8;" }
      );

      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");

      link.href = url;
      link.download = `crop-harvest-${this.dateString(new Date())}.csv`;
      link.click();

      URL.revokeObjectURL(url);
    },
  },

  async mounted() {
    await this.loadCrops();
    await this.loadLists();
  },
};
</script>
