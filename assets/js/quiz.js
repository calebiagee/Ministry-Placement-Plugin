/* jshint esversion:6 */
/* global mpqData */
(function () {
  'use strict';

  /* ------------------------------------------------------------------ */
  /* State                                                                */
  /* ------------------------------------------------------------------ */
  var questions   = mpqData.questions;
  var answers     = {};
  var current     = 0;
  var total       = questions.length;
  var exiting     = false;
  var resultsData = null;

  /* ------------------------------------------------------------------ */
  /* DOM refs                                                             */
  /* ------------------------------------------------------------------ */
  var app          = document.getElementById('mpq-app');
  var welcome      = document.getElementById('mpq-welcome');
  var quizScreen   = document.getElementById('mpq-quiz');
  var resultsScr   = document.getElementById('mpq-results');
  var confirmScr   = document.getElementById('mpq-confirmation');
  var loading      = document.getElementById('mpq-loading');
  var startBtn     = document.getElementById('mpq-start-btn');
  var nextBtn      = document.getElementById('mpq-next-btn');
  var backBtn      = document.getElementById('mpq-back-btn');
  var qWrap        = document.getElementById('mpq-question-wrap');
  var qCounter     = document.getElementById('mpq-q-counter');
  var progressFill = document.getElementById('mpq-progress-fill');
  var resultsCont  = document.getElementById('mpq-results-content');
  var confirmCta   = document.getElementById('mpq-confirmation__cta');

  /* ------------------------------------------------------------------ */
  /* Helpers                                                              */
  /* ------------------------------------------------------------------ */
  function showScreen(id) {
    var screens = [welcome, quizScreen, resultsScr, confirmScr];
    screens.forEach(function (s) {
      s.style.display = 'none';
      s.classList.remove('mpq-screen--active');
    });
    var target = document.getElementById(id);
    if (target) {
      target.style.display = 'flex';
      target.classList.add('mpq-screen--active');
    }
    try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch(e) { window.scrollTo(0, 0); }
  }

  function setLoading(on) {
    if (on) {
      loading.classList.remove('mpq-loading--hidden');
      loading.removeAttribute('aria-hidden');
    } else {
      loading.classList.add('mpq-loading--hidden');
      loading.setAttribute('aria-hidden', 'true');
    }
  }

  function updateProgress() {
    var pct = Math.round((current / total) * 100);
    progressFill.style.width = pct + '%';
    progressFill.parentElement.setAttribute('aria-valuenow', pct);
    qCounter.textContent = 'Question ' + (current + 1) + ' of ' + total;
    backBtn.style.visibility = current === 0 ? 'hidden' : 'visible';
  }

  function isAnswered(q) {
    if (q.type === 'multi') {
      return Array.isArray(answers[q.id]) && answers[q.id].length > 0;
    }
    return answers[q.id] !== undefined && answers[q.id] !== null && answers[q.id] !== '';
  }

  /* ------------------------------------------------------------------ */
  /* Question rendering                                                   */
  /* ------------------------------------------------------------------ */
  function renderQuestion(idx) {
    var q = questions[idx];
    var typeLabel = q.type === 'scale' ? 'Spiritual Gifts' : 'About You';

    var html = '<div class="mpq-question">'
      + '<span class="mpq-question__type">' + typeLabel + '</span>'
      + '<p class="mpq-question__text">' + escHtml(q.text) + '</p>';

    if (q.type === 'scale') {
      html += renderScale(q);
    } else if (q.type === 'multi') {
      html += renderMulti(q);
    } else {
      html += renderChoices(q);
    }
    html += '</div>';

    var isLast = idx === total - 1;

    /* For multi questions the Next button lives inline; hide the footer one */
    nextBtn.style.display = (q.type === 'multi') ? 'none' : '';
    nextBtn.disabled = !isAnswered(q);
    nextBtn.innerHTML = isLast
      ? 'See My Results <span class="mpq-btn__arrow">&#8594;</span>'
      : 'Next <span class="mpq-btn__arrow">&#8594;</span>';

    if (qWrap.firstChild && !exiting) {
      exiting = true;
      var old = qWrap.firstChild;
      old.classList.add('mpq-question--exit');
      setTimeout(function () {
        qWrap.innerHTML = html;
        attachHandlers(q, isLast);
        updateProgress();
        exiting = false;
      }, 260);
    } else if (!exiting) {
      qWrap.innerHTML = html;
      attachHandlers(q, isLast);
      updateProgress();
    }
  }

  function renderScale(q) {
    var labels = ['Not at all', '', 'Sometimes', '', 'Very much me'];
    var html = '<div class="mpq-scale__label-row">';
    labels.forEach(function (l) { html += '<span>' + escHtml(l) + '</span>'; });
    html += '</div><div class="mpq-scale__options" role="group" aria-label="Rate from 1 to 5">';
    for (var i = 1; i <= 5; i++) {
      var sel = answers[q.id] === i ? ' selected' : '';
      html += '<button class="mpq-scale__btn' + sel + '" data-val="' + i + '" aria-label="' + i + '">' + i + '</button>';
    }
    html += '</div>';
    return html;
  }

  var choiceKeys = ['A', 'B', 'C', 'D', 'E'];

  function renderMulti(q) {
    var selected = Array.isArray(answers[q.id]) ? answers[q.id] : [];
    var hasAnswer = selected.length > 0;
    var html = '<p class="mpq-multi__hint">Select all that apply</p>'
      + '<div class="mpq-choices" role="group">';
    q.options.forEach(function (opt, i) {
      var isSel = selected.indexOf(opt.value) !== -1;
      var selCls = isSel ? ' selected' : '';
      var keyHtml = isSel ? '&#10003;' : choiceKeys[i];
      html += '<button class="mpq-choice__btn' + selCls + '" data-val="' + escAttr(opt.value) + '" type="button">'
        + '<span class="mpq-choice__key">' + keyHtml + '</span>'
        + '<span>' + escHtml(opt.label) + '</span>'
        + '</button>';
    });
    html += '</div>'
      + '<div class="mpq-multi__next">'
      + '<button class="mpq-btn mpq-btn--primary mpq-multi__next-btn" type="button"'
      + (hasAnswer ? '' : ' disabled') + '>'
      + 'Next <span class="mpq-btn__arrow">&#8594;</span>'
      + '</button>'
      + '</div>';
    return html;
  }

  function renderChoices(q) {
    var html = '<div class="mpq-choices" role="group">';
    q.options.forEach(function (opt, i) {
      var sel = answers[q.id] === opt.value ? ' selected' : '';
      html += '<button class="mpq-choice__btn' + sel + '" data-val="' + escAttr(opt.value) + '">'
        + '<span class="mpq-choice__key">' + choiceKeys[i] + '</span>'
        + '<span>' + escHtml(opt.label) + '</span>'
        + '</button>';
    });
    html += '</div>';
    return html;
  }

  function attachHandlers(q, isLast) {
    if (q.type === 'scale') {
      qWrap.querySelectorAll('.mpq-scale__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          qWrap.querySelectorAll('.mpq-scale__btn').forEach(function (b) { b.classList.remove('selected'); });
          btn.classList.add('selected');
          answers[q.id] = parseInt(btn.getAttribute('data-val'), 10);
          nextBtn.disabled = false;
          setTimeout(advance, 320);
        });
      });
    } else if (q.type === 'multi') {
      var multiNextBtn = qWrap.querySelector('.mpq-multi__next-btn');
      if (multiNextBtn) {
        multiNextBtn.innerHTML = (isLast ? 'See My Results' : 'Next')
          + ' <span class="mpq-btn__arrow">&#8594;</span>';
        multiNextBtn.addEventListener('click', advance);
      }
      var allBtns = qWrap.querySelectorAll('.mpq-choice__btn');
      allBtns.forEach(function (btn, btnIdx) {
        btn.addEventListener('click', function () {
          var val = btn.getAttribute('data-val');
          var cur = Array.isArray(answers[q.id]) ? answers[q.id].slice() : [];
          var pos = cur.indexOf(val);
          if (pos === -1) {
            cur.push(val);
            btn.classList.add('selected');
            btn.querySelector('.mpq-choice__key').innerHTML = '&#10003;';
          } else {
            cur.splice(pos, 1);
            btn.classList.remove('selected');
            btn.querySelector('.mpq-choice__key').textContent = choiceKeys[btnIdx];
          }
          answers[q.id] = cur;
          if (multiNextBtn) multiNextBtn.disabled = cur.length === 0;
        });
      });
    } else {
      qWrap.querySelectorAll('.mpq-choice__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          qWrap.querySelectorAll('.mpq-choice__btn').forEach(function (b) { b.classList.remove('selected'); });
          btn.classList.add('selected');
          answers[q.id] = btn.getAttribute('data-val');
          nextBtn.disabled = false;
          setTimeout(advance, 320);
        });
      });
    }
  }

  /* ------------------------------------------------------------------ */
  /* Navigation                                                           */
  /* ------------------------------------------------------------------ */
  function advance() {
    if (!isAnswered(questions[current])) return;
    if (current < total - 1) {
      current++;
      renderQuestion(current);
    } else {
      submitQuiz();
    }
  }

  startBtn.addEventListener('click', function () {
    showScreen('mpq-quiz');
    renderQuestion(0);
  });

  nextBtn.addEventListener('click', advance);

  backBtn.addEventListener('click', function () {
    if (current > 0) {
      current--;
      renderQuestion(current);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (quizScreen.style.display === 'none') return;
    var q = questions[current];
    if (e.key === 'Enter' && isAnswered(q)) { advance(); return; }

    if (q.type === 'scale') {
      var n = parseInt(e.key, 10);
      if (n >= 1 && n <= 5) {
        var target = qWrap.querySelector('.mpq-scale__btn[data-val="' + n + '"]');
        if (target) target.click();
      }
    }

    if (q.type === 'choice' || q.type === 'multi') {
      var map = { a: 0, b: 1, c: 2, d: 3, e: 4 };
      var idx = map[e.key.toLowerCase()];
      if (idx !== undefined) {
        var btns = qWrap.querySelectorAll('.mpq-choice__btn');
        if (btns[idx]) btns[idx].click();
      }
    }
  });

  /* ------------------------------------------------------------------ */
  /* Quiz submit → calculate results                                      */
  /* ------------------------------------------------------------------ */
  function submitQuiz() {
    setLoading(true);
    showScreen('mpq-results');

    var formData = new FormData();
    formData.append('action', 'mpq_submit');
    formData.append('nonce', mpqData.nonce);
    Object.keys(answers).forEach(function (k) {
      var v = answers[k];
      if (Array.isArray(v)) {
        v.forEach(function (item) { formData.append('answers[' + k + '][]', item); });
      } else {
        formData.append('answers[' + k + ']', v);
      }
    });

    fetch(mpqData.ajaxUrl, { method: 'POST', body: formData })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        setLoading(false);
        if (data.success) {
          resultsData = data.data;
          renderResults(data.data);
        } else {
          alert('Something went wrong. Please try again.');
        }
      })
      .catch(function () {
        setLoading(false);
        alert('Network error. Please check your connection and try again.');
      });
  }

  /* ------------------------------------------------------------------ */
  /* Results rendering                                                    */
  /* ------------------------------------------------------------------ */
  function renderResults(data) {
    var topGifts      = data.top_gifts;
    var topMinistries = data.top_ministries;
    var allGifts      = data.all_gifts;

    var rankLabels   = ['Your #1 Gift', 'Your #2 Gift', 'Your #3 Gift'];
    var mBadges      = ['Best Match', 'Great Fit', 'Strong Fit'];
    var mRankClasses = ['mpq-ministry-card--rank-1', 'mpq-ministry-card--rank-2', 'mpq-ministry-card--rank-3'];

    var html = '<p class="mpq-results__eyebrow">Your Results</p>'
      + '<h2 class="mpq-results__title">Here\'s How God<br>Has Wired You</h2>'
      + '<p class="mpq-results__subtitle">Based on your answers, here are your top spiritual gifts and the Hope Church ministries where you\'d likely thrive.</p>';

    /* ---- Top Gifts ---- */
    html += '<p class="mpq-section-title" style="margin-top:0">Your Top Spiritual Gifts</p>';
    html += '<div class="mpq-gifts-grid">';
    topGifts.forEach(function (g, i) {
      html += '<div class="mpq-gift-card mpq-gift-card--rank-' + (i + 1) + '">'
        + '<div class="mpq-gift-card__rank">' + rankLabels[i] + '</div>'
        + '<div class="mpq-gift-card__name">' + escHtml(g.name) + '</div>'
        + '<div class="mpq-gift-card__pct">' + g.percentage + '%</div>'
        + '<p class="mpq-gift-card__desc">' + escHtml(g.description) + '</p>'
        + '</div>';
    });
    html += '</div>';

    /* ---- Ministry recommendations ---- */
    html += '<p class="mpq-section-title">Recommended Ministries for You</p>'
      + '<p class="mpq-results__subtitle" style="margin-top:0;margin-bottom:1.5rem;">Check the ministries you\'re interested in, then fill out your info below so our team can connect with you.</p>';

    html += '<div class="mpq-ministries-list" id="mpq-ministry-cards">';
    topMinistries.forEach(function (m, i) {
      html += '<label class="mpq-ministry-card ' + mRankClasses[i] + '" for="mpq-m-' + escAttr(m.id) + '">';

      /* Only render image area if there's an actual image URL */
      if (m.image_url) {
        html += '<div class="mpq-ministry-card__image" style="background-image:url(' + m.image_url + ')">';
        if (m.logo_url) {
          html += '<img src="' + m.logo_url + '" class="mpq-ministry-card__logo" alt="' + escAttr(m.name) + ' logo">';
        }
        html += '<span class="mpq-ministry-card__badge">' + mBadges[i] + '</span>'
          + '</div>';
      }

      html += '<div class="mpq-ministry-card__body">';
      if (!m.image_url) {
        html += '<span class="mpq-ministry-card__badge">' + mBadges[i] + '</span>';
      }
      html += '<div class="mpq-ministry-card__name">' + escHtml(m.name) + '</div>'
        + '<p class="mpq-ministry-card__desc">' + escHtml(m.description) + '</p>'
        + '<span class="mpq-ministry-card__commitment">' + escHtml(m.commitment) + '</span>'
        + '</div>'
        + '<div class="mpq-ministry-card__check">'
        + '<input type="checkbox" id="mpq-m-' + escAttr(m.id) + '" name="ministry" value="' + escAttr(m.id) + '" class="mpq-ministry-check">'
        + '<span class="mpq-ministry-check__box"></span>'
        + '<span class="mpq-ministry-check__label">I\'m interested</span>'
        + '</div>'
        + '</label>';
    });
    html += '</div>';

    /* ---- All gifts bar chart ---- */
    html += '<p class="mpq-section-title">Your Full Gifts Profile</p>'
      + '<div class="mpq-all-gifts">';
    allGifts.forEach(function (g) {
      html += '<div class="mpq-gift-bar-row">'
        + '<span class="mpq-gift-bar__label">' + escHtml(g.name) + '</span>'
        + '<div class="mpq-gift-bar__track"><div class="mpq-gift-bar__fill" data-pct="' + g.percentage + '"></div></div>'
        + '<span class="mpq-gift-bar__pct">' + g.percentage + '%</span>'
        + '</div>';
    });
    html += '</div>';

    /* ---- Connect form ---- */
    html += '<div class="mpq-connect-form" id="mpq-connect-form">'
      + '<h3 class="mpq-connect-form__title">Ready to Take the Next Step?</h3>'
      + '<p class="mpq-connect-form__desc">Enter your info and our team will reach out to help get you connected!</p>'
      + '<div id="mpq-connect-error" class="mpq-connect-error" style="display:none;"></div>'
      + '<div class="mpq-form-row">'
      + '<div class="mpq-form-field">'
      + '<label for="mpq-name">Your Name</label>'
      + '<input type="text" id="mpq-name" placeholder="First and last name" autocomplete="name">'
      + '</div>'
      + '<div class="mpq-form-field">'
      + '<label for="mpq-email">Email Address</label>'
      + '<input type="email" id="mpq-email" placeholder="your@email.com" autocomplete="email">'
      + '</div>'
      + '</div>'
      + '<button class="mpq-btn mpq-btn--primary" id="mpq-connect-btn">Connect Me &rarr;</button>'
      + '</div>';

    resultsCont.innerHTML = html;

    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        resultsCont.querySelectorAll('.mpq-gift-bar__fill').forEach(function (bar) {
          bar.style.width = bar.getAttribute('data-pct') + '%';
        });
      });
    });

    document.getElementById('mpq-connect-btn').addEventListener('click', handleConnect);
  }

  /* ------------------------------------------------------------------ */
  /* Connect (save + email)                                               */
  /* ------------------------------------------------------------------ */
  function handleConnect() {
    var nameEl  = document.getElementById('mpq-name');
    var emailEl = document.getElementById('mpq-email');
    var errEl   = document.getElementById('mpq-connect-error');

    var name  = nameEl  ? nameEl.value.trim()  : '';
    var email = emailEl ? emailEl.value.trim() : '';

    var checked = resultsCont.querySelectorAll('.mpq-ministry-check:checked');
    var selIds  = [];
    checked.forEach(function (cb) { selIds.push(cb.value); });

    var errors = [];
    if (!name)               errors.push('Please enter your name.');
    if (!isValidEmail(email)) errors.push('Please enter a valid email address.');
    if (selIds.length === 0)  errors.push('Please select at least one ministry you\'re interested in.');

    if (errors.length) {
      errEl.innerHTML  = errors.map(function (e) { return '<p>' + escHtml(e) + '</p>'; }).join('');
      errEl.style.display = 'block';
      errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    errEl.style.display = 'none';
    setLoading(true);

    var d   = resultsData;
    var recIds = (d.top_ministries || []).map(function (m) { return m.id; }).join(',');

    var formData = new FormData();
    formData.append('action',          'mpq_confirm');
    formData.append('nonce',           mpqData.nonce);
    formData.append('name',            name);
    formData.append('email',           email);
    formData.append('rec_ministries',  recIds);
    formData.append('sel_ministries',  selIds.join(','));
    formData.append('top_gifts',       JSON.stringify(d.top_gifts || []));
    formData.append('answers_json',    JSON.stringify(answers));

    fetch(mpqData.ajaxUrl, { method: 'POST', body: formData })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        setLoading(false);
        if (data.success) {
          renderConfirmation(mpqData.connectUrl);
          showScreen('mpq-confirmation');
        } else {
          var msg = (data.data && data.data.message) ? data.data.message : 'Something went wrong. Please try again.';
          alert(msg);
        }
      })
      .catch(function () {
        setLoading(false);
        alert('Network error. Please check your connection and try again.');
      });
  }

  function renderConfirmation(connectUrl) {
    var ctaHtml = '';
    if (connectUrl) {
      ctaHtml = '<a href="' + escAttr(connectUrl) + '" class="mpq-btn mpq-btn--primary">Visit Our Connect Page &rarr;</a>';
    }
    if (confirmCta) confirmCta.innerHTML = ctaHtml;
  }

  /* ------------------------------------------------------------------ */
  /* Security helpers                                                     */
  /* ------------------------------------------------------------------ */
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function escAttr(str) {
    return String(str).replace(/"/g, '&quot;');
  }

  function isValidEmail(e) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e);
  }

}());
