<template>
  <div class="todo-wrapper" :class="currentTheme">
    <div class="todo-card">
      <header class="todo-header">
        <div class="header-top">
          <span class="label">ЗАДАЧІ</span>
          <div class="header-right">
            <span class="counter">{{ completedCount }}/{{ tasks.length }}</span>
            <button class="theme-toggle" @click="toggleTheme" :title="isDark ? 'Світла тема' : 'Темна тема'">
              <!-- Sun -->
              <svg v-if="isDark" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="10" cy="10" r="4" stroke="currentColor" stroke-width="1.5"/>
                <path d="M10 2V4M10 16V18M2 10H4M16 10H18M4.22 4.22L5.64 5.64M14.36 14.36L15.78 15.78M15.78 4.22L14.36 5.64M5.64 14.36L4.22 15.78" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <!-- Moon -->
              <svg v-else viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 11.5A7 7 0 1 1 8.5 3a5.5 5.5 0 0 0 8.5 8.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
        </div>
        <h1 class="title">Чек лист</h1>
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercent + '%' }"></div>
        </div>

      </header>

      <TransitionGroup name="task" tag="ul" class="task-list">
        <li
          v-for="task in sortedTasks"
          :key="task.id"
          class="task-item"
          :class="{ completed: task.done }"

        >
          <div @click="toggleTask(task)" class="checkbox" :class="{ checked: task.done }">
            <svg v-if="task.done" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 5L4.5 8.5L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <span class="task-text">{{ task.name }} &nbsp;
            <span class="exp-days">{{ task.expDays }} дні(в)</span>

             </span>
             <button type="button" class="btn btn-outline-dark p-1">
                    Перевести на уцінку
                </button>
          <span class="task-tag" v-if="task.end">{{ task.end }}</span>
        </li>
      </TransitionGroup>


    </div>
  </div>
</template>

<script>
export default {
  name: 'TodoList',

  props: {
    initialTasks: {
      type: Array,
      default: () => []
    },
    // 'light' | 'dark'  — за замовчуванням світла тема
    theme: {
      type: String,
      default: 'dark',
      validator: (v) => ['light', 'dark'].includes(v)
    }
  },

  data() {
    return {
      tasks: [],
      newTaskText: '',
      nextId: null,
      internalTheme: this.theme
    }
  },

  computed: {

    isDark() {
      return this.internalTheme === 'dark'
    },
    currentTheme() {
      return this.internalTheme === 'dark' ? 'theme-dark' : 'theme-light'
    },
    completedCount() {
      return this.tasks.filter(t => t.done).length
    },
    progressPercent() {
      if (!this.tasks.length) return 0
      return Math.round((this.completedCount / this.tasks.length) * 100)
    },
    // невиконані — зверху, виконані — знизу; порядок усередині груп зберігається
    sortedTasks() {
      return [...this.tasks].sort((a, b) => {
        if (a.done === b.done) return 0
        return a.done ? 1 : -1
      })
    }
  },

  watch: {
    theme(val) {
      this.internalTheme = val
    }
  },

  created() {
    this.tasks = this.initialTasks.map((item, index) => {
      if (typeof item === 'string') {
        return { id: index + 1, text: item, done: false, tag: null }
      }
      return {
        id: item.id ?? index + 1,
        text: item.text ?? item.title ?? String(item),
        done: item.done ?? item.completed ?? false,
        tag: item.tag ?? item.category ?? null
      }
    })
    this.nextId = this.tasks.length + 1
  },

  methods: {
    loadList() {
        axios.get(route('date.get.todo')).then(resp => {
            this.tasks = resp.data.data;
        }).catch(err => {
            console.error(err);
            this.tasks = [];
        })
    },
    toggleTask(task) {
      task.done = !task.done;
      axios.get(route('date.set.done', {dateProduct: task.id})).then(resp => {
        console.debug(reso);
      })
      this.$emit('task-toggled', { ...task })
    },

    addTask() {
      const text = this.newTaskText.trim()
      if (!text) return
      const newTask = { id: this.nextId++, text, done: false, tag: null }
      this.tasks.push(newTask)
      this.newTaskText = ''
      this.$emit('task-added', { ...newTask })
    },

    toggleTheme() {
      this.internalTheme = this.internalTheme === 'dark' ? 'light' : 'dark'
      this.$emit('theme-change', this.internalTheme)
    }
  },
  mounted() {
    this.loadList();
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap');

/* ══════════════════════════════════════════
   CSS-змінні: світла тема (за замовчуванням)
   ══════════════════════════════════════════ */
.theme-light {
  --bg-page:          #f0ede8;
  --bg-page-g1:       rgba(255, 200, 80, 0.10);
  --bg-page-g2:       rgba(255, 100, 60, 0.06);
  --bg-card:          #ffffff;
  --bg-item:          transparent;
  --bg-item-hover:    #f4f1ec;
  --bg-item-done:     #f7f5f0;
  --bg-input:         #f4f1ec;
  --bg-tag:           #ede9e2;

  --border-card:      #e5e0d8;
  --border-item:      #ece8e0;
  --border-item-done: #e8e4dc;
  --border-input:     #ddd8cf;
  --border-tag:       #ddd8cf;

  --text-title:       #1a1714;
  --text-body:        #3d3730;
  --text-done:        #b0a898;
  --text-label:       #a09880;
  --text-tag:         #8a7f70;
  --text-placeholder: #b8b0a0;

  --accent-from:      #f5c842;
  --accent-to:        #ff7a3c;
  --accent-counter:   #c47a20;
  --accent-glow:      rgba(245, 180, 50, 0.25);

  --progress-bg:      #e8e4dc;
  --shadow-card:      0 4px 6px rgba(0,0,0,0.04), 0 16px 40px rgba(0,0,0,0.08);
  --checkbox-border:  #c8c0b0;
}

/* ══════════════════════
   CSS-змінні: темна тема
   ══════════════════════ */
.theme-dark {
  --bg-page:          #0d0d0d;
  --bg-page-g1:       rgba(255, 220, 80, 0.07);
  --bg-page-g2:       rgba(255, 100, 60, 0.05);
  --bg-card:          #141414;
  --bg-item:          transparent;
  --bg-item-hover:    #1a1a1a;
  --bg-item-done:     #161616;
  --bg-input:         #1a1a1a;
  --bg-tag:           #1e1e1e;

  --border-card:      #2a2a2a;
  --border-item:      #202020;
  --border-item-done: #1e1e1e;
  --border-input:     #2a2a2a;
  --border-tag:       #2a2a2a;

  --text-title:       #f0f0f0;
  --text-body:        #d0d0d0;
  --text-done:        #444444;
  --text-label:       #555555;
  --text-tag:         #555555;
  --text-placeholder: #3a3a3a;

  --accent-from:      #ffdc50;
  --accent-to:        #ff9a3c;
  --accent-counter:   #ffdc50;
  --accent-glow:      rgba(255, 180, 50, 0.30);

  --progress-bg:      #222222;
  --shadow-card:      0 0 0 1px rgba(255,255,255,0.03), 0 32px 64px rgba(0,0,0,0.6);
  --checkbox-border:  #333333;
}

.exp-days {
    padding: .5rem;
    background: var(--accent-to);
    color: var(--border-tag);
    border-radius: 4px;
}

/* ══════════
   Base reset
   ══════════ */
* { box-sizing: border-box; margin: 0; padding: 0; }

/* ══════════
   Wrapper
   ══════════ */
.todo-wrapper {
  min-height: 100vh;
  display: flex;

  justify-content: center;
  background-color: var(--bg-page);
  background-image:
    radial-gradient(ellipse 60% 40% at 70% 20%, var(--bg-page-g1) 0%, transparent 60%),
    radial-gradient(ellipse 40% 60% at 20% 80%, var(--bg-page-g2) 0%, transparent 60%);
  padding: .5rem;
  font-family: 'Syne', sans-serif;
  transition: background-color 0.35s ease;
}

/* ══════════
   Card
   ══════════ */
.todo-card {
  width: 100%;
max-width: 720px;
  background: var(--bg-card);
  border: 1px solid var(--border-card);
  border-radius: 8px;
  padding: .5rem;
  box-shadow: var(--shadow-card);
  transition: background 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
}

/* ══════════
   Header
   ══════════ */
.todo-header { margin-bottom: 1.75rem; }

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.label {
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  color: var(--text-label);
  font-family: 'DM Mono', monospace;
  transition: color 0.35s ease;
}

.counter {
  font-family: 'DM Mono', monospace;
  font-size: 0.75rem;
  color: var(--accent-counter);
  font-weight: 500;
  transition: color 0.35s ease;
}

/* ── Theme toggle ── */
.theme-toggle {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  border: 1px solid var(--border-item);
  background: var(--bg-input);
  color: var(--text-label);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, border-color 0.2s, color 0.2s, transform 0.2s;
}
.theme-toggle:hover { color: var(--text-body); transform: rotate(20deg) scale(1.1); }
.theme-toggle svg { width: 15px; height: 15px; }

.title {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-title);
  letter-spacing: -0.03em;
  margin-bottom: 1rem;
  transition: color 0.35s ease;
}

.progress-bar {
  height: 3px;
  background: var(--progress-bg);
  border-radius: 99px;
  overflow: hidden;
  transition: background 0.35s ease;
}
.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
  border-radius: 99px;
  transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ══════════
   Task list
   ══════════ */
.task-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  position: relative;
}

.task-item {
  display: flex;
  align-items: center;
  gap: 0.875rem;
  padding: 0.875rem 1rem;
  border-radius: 12px;
  border: 1px solid var(--border-item);
  background: var(--bg-item);
  cursor: pointer;
  user-select: none;
  transition: background 0.2s ease, border-color 0.2s ease;
}
.task-item:hover { background: var(--bg-item-hover); border-color: var(--border-input); }
.task-item.completed { background: var(--bg-item-done); border-color: var(--border-item-done); }
.task-item.completed .task-text {
  color: var(--text-done);
  text-decoration: line-through;
  text-decoration-color: var(--text-done);
}

/* ── Checkbox ── */
.checkbox {
  width: 22px;
  height: 22px;
  min-width: 22px;
  border-radius: 7px;
  border: 1.5px solid var(--checkbox-border);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  background: transparent;
}
.checkbox.checked {
  background: linear-gradient(135deg, var(--accent-from), var(--accent-to));
  border-color: transparent;
  color: #0d0d0d;
  box-shadow: 0 4px 12px var(--accent-glow);
}
.checkbox svg { width: 12px; height: 10px; }

/* ── Text & tag ── */
.task-text {
  flex: 1;
  font-size: 0.925rem;
  color: var(--text-body);
  font-weight: 400;
  line-height: 1.4;
  transition: color 0.2s ease;
}
.task-tag {
  font-family: 'DM Mono', monospace;
  font-size: 0.65rem;
  font-weight: 500;
  color: var(--text-tag);
  background: var(--bg-tag);
  border: 1px solid var(--border-tag);
  border-radius: 6px;
  padding: 0.2rem 0.5rem;
  white-space: nowrap;
  transition: background 0.35s ease, color 0.35s ease;
}

/* ══════════
   Add task
   ══════════ */
.add-task { display: flex; gap: 0.625rem; }

.add-task input {
  flex: 1;
  background: var(--bg-input);
  border: 1px solid var(--border-input);
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-family: 'Syne', sans-serif;
  font-size: 0.875rem;
  color: var(--text-body);
  outline: none;
  transition: border-color 0.2s, background 0.35s, color 0.35s;
}
.add-task input::placeholder { color: var(--text-placeholder); }
.add-task input:focus { border-color: var(--checkbox-border); }

.add-task button {
  width: 44px;
  height: 44px;
  min-width: 44px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, var(--accent-from), var(--accent-to));
  color: #0d0d0d;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity 0.2s, transform 0.15s;
}
.add-task button:hover:not(:disabled) { opacity: 0.88; transform: scale(1.05); }
.add-task button:disabled { opacity: 0.25; cursor: default; }
.add-task button svg { width: 14px; height: 14px; }

/* ══════════════════════════════════
   TransitionGroup — анімація задач
   ══════════════════════════════════ */
.task-move        { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.task-enter-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.task-leave-active {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  position: absolute;
  width: calc(100% - 4rem);
}
.task-enter-from  { opacity: 0; transform: translateY(-8px); }
.task-leave-to    { opacity: 0; transform: translateX(12px); }
</style>
