// ============================================================
// Bure Town Tax Collection Management System
// Main JavaScript File
// ============================================================

'use strict';

// ---- Global TCMS Object ----
const TCMS = {
  // Initialize all components
  init() {
    this.initAlerts();
    this.initModals();
    this.initTopbarTime();
    this.initTables();
    this.initFormValidation();
    this.initSidebar();
    this.initToasts();
    this.markActiveNav();
  },

  // ---- Auto-dismiss Alerts ----
  initAlerts() {
    document.querySelectorAll('.alert[data-dismiss]').forEach(el => {
      setTimeout(() => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
      }, parseInt(el.dataset.dismiss) || 4000);
    });
  },

  // ---- Modal Management ----
  initModals() {
    // Open modal
    document.querySelectorAll('[data-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.modal;
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('open');
      });
    });
    // Close modal via overlay click or close button
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.classList.remove('open');
      });
    });
    document.querySelectorAll('.modal-close').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.closest('.modal-overlay')?.classList.remove('open');
      });
    });
    // Escape key
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
      }
    });
  },

  // ---- Topbar Digital Clock ----
  initTopbarTime() {
    const el = document.getElementById('topbar-time');
    if (!el) return;
    const update = () => {
      const now = new Date();
      el.textContent = now.toLocaleDateString('en-ET', {
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
      }) + '  ' + now.toLocaleTimeString('en-ET', { hour12: false });
    };
    update();
    setInterval(update, 1000);
  },

  // ---- DataTable-like Search & Sort ----
  initTables() {
    document.querySelectorAll('[data-searchable]').forEach(tableId => {
      const input = document.querySelector(`[data-search-for="${tableId}"]`);
      if (!input) return;
      input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        const table = document.getElementById(tableId);
        if (!table) return;
        table.querySelectorAll('tbody tr').forEach(row => {
          row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    });
  },

  // ---- Form Validation ----
  initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
      form.addEventListener('submit', e => {
        let valid = true;
        form.querySelectorAll('[required]').forEach(field => {
          if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
          } else {
            field.classList.remove('is-invalid');
          }
        });
        if (!valid) {
          e.preventDefault();
          TCMS.showToast('Please fill in all required fields.', 'danger');
        }
      });
    });
  },

  // ---- Sidebar Toggle (Mobile) ----
  initSidebar() {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
      toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
  },

  // ---- Mark Active Nav Link ----
  markActiveNav() {
    const path = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
      const href = link.getAttribute('href')?.split('/').pop();
      if (href && href === path) link.classList.add('active');
    });
  },

  // ---- Toast Notifications ----
  initToasts() {
    if (!document.getElementById('toast-container')) {
      const tc = document.createElement('div');
      tc.id = 'toast-container';
      tc.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
      document.body.appendChild(tc);
    }
  },

  showToast(message, type = 'info', duration = 3500) {
    const icons = { success: '✅', danger: '❌', warning: '⚠️', info: 'ℹ️' };
    const colors = {
      success: '#2dc653', danger: '#dc3545', warning: '#ffc107', info: '#17a2b8'
    };
    const toast = document.createElement('div');
    toast.style.cssText = `
      display:flex;align-items:center;gap:10px;
      background:#fff;border-radius:10px;padding:12px 18px;
      box-shadow:0 6px 24px rgba(0,0,0,0.15);
      border-left:4px solid ${colors[type] || colors.info};
      min-width:260px;max-width:360px;
      animation:slideIn .3s ease;
      font-size:.875rem;color:#1a1a2e;
    `;
    toast.innerHTML = `<span style="font-size:1.1rem">${icons[type] || icons.info}</span><span>${message}</span>`;
    const container = document.getElementById('toast-container');
    if (container) {
      container.appendChild(toast);
      setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .4s'; setTimeout(() => toast.remove(), 400); }, duration);
    }
  },

  // ---- AJAX Helper ----
  async request(url, data = {}, method = 'POST') {
    const formData = new FormData();
    Object.entries(data).forEach(([k, v]) => formData.append(k, v));
    try {
      const res = await fetch(url, { method, body: method === 'POST' ? formData : undefined });
      return await res.json();
    } catch (err) {
      console.error('AJAX Error:', err);
      return { success: false, message: 'Network error' };
    }
  },

  // ---- Confirm Dialog ----
  confirm(message, onConfirm) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay open';
    overlay.innerHTML = `
      <div class="modal" style="width:400px;">
        <div class="modal-header">
          <h3>⚠️ Confirm Action</h3>
        </div>
        <div class="modal-body">
          <p style="font-size:.9rem;color:#555;">${message}</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary cancel-btn">Cancel</button>
          <button class="btn btn-danger confirm-btn">Confirm</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    overlay.querySelector('.cancel-btn').onclick = () => overlay.remove();
    overlay.querySelector('.confirm-btn').onclick = () => { overlay.remove(); onConfirm(); };
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });
  },

  // ---- Format Currency (ETB) ----
  formatCurrency(amount) {
    return 'ETB ' + parseFloat(amount || 0).toLocaleString('en-ET', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  },

  // ---- Print Receipt ----
  printReceipt(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const w = window.open('', '_blank');
    w.document.write(`
      <html><head><title>Tax Receipt</title>
      <link rel="stylesheet" href="../assets/css/style.css">
      </head><body style="background:#fff;">${el.outerHTML}</body></html>
    `);
    w.document.close();
    w.onload = () => { w.print(); w.close(); };
  }
};

// CSS animation injection
const style = document.createElement('style');
style.textContent = `
@keyframes slideIn {
  from { transform: translateX(30px); opacity: 0; }
  to   { transform: translateX(0);    opacity: 1; }
}`;
document.head.appendChild(style);

// ---- Tax Calculator Utility ----
const TaxCalc = {
  // Ethiopian tax brackets (monthly income)
  brackets: [
    { min: 0,     max: 600,   rate: 0,    fixed: 0      },
    { min: 601,   max: 1650,  rate: 0.10, fixed: 0      },
    { min: 1651,  max: 3200,  rate: 0.15, fixed: 105    },
    { min: 3201,  max: 5250,  rate: 0.20, fixed: 337.50 },
    { min: 5251,  max: 7800,  rate: 0.25, fixed: 747.50 },
    { min: 7801,  max: 10900, rate: 0.30, fixed: 1382.50},
    { min: 10901, max: null,  rate: 0.35, fixed: 2312.50},
  ],

  calculate(monthlyIncome) {
    const inc = parseFloat(monthlyIncome) || 0;
    if (inc <= 0) return { income: 0, tax: 0, rate: 0, net: 0 };
    for (const b of this.brackets) {
      if (b.max === null || inc <= b.max) {
        const tax = b.rate > 0 ? (inc - b.min) * b.rate + b.fixed : 0;
        return {
          income: inc,
          tax: parseFloat(tax.toFixed(2)),
          rate: b.rate * 100,
          net: parseFloat((inc - tax).toFixed(2)),
        };
      }
    }
    return { income: inc, tax: 0, rate: 0, net: inc };
  },

  calculateAnnual(annualIncome) {
    const monthly = parseFloat(annualIncome) / 12;
    const monthlyResult = this.calculate(monthly);
    return {
      income: parseFloat(annualIncome),
      tax: parseFloat((monthlyResult.tax * 12).toFixed(2)),
      rate: monthlyResult.rate,
      net: parseFloat((monthlyResult.net * 12).toFixed(2)),
    };
  }
};

// ---- DOM Ready ----
document.addEventListener('DOMContentLoaded', () => TCMS.init());
