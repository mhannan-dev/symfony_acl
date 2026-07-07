<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>

    <div v-if="loading" class="text-slate-500">Loading...</div>

    <template v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Users</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.users }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Groups</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.groups }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Permissions</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.permissions }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Activity Logs</span>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ stats.recentActivity }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <h3 class="text-sm font-semibold text-slate-700 mb-4">Users per Group</h3>
          <apexchart type="pie" height="280" :options="pieOptions" :series="usersPerGroupSeries" />
        </div>


        <div class="bg-white rounded-xl border border-slate-200 p-6">
          <h3 class="text-sm font-semibold text-slate-700 mb-4">Permissions per Content Type</h3>
          <apexchart type="pie" height="280" :options="pieOptions" :series="permsPerTypeSeries" />
        </div>

        
      </div>

      <div class="mt-6 bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Users Organization</h3>
        <div id="apex-tree-container" class="w-full" style="height: 500px; overflow: hidden;"></div>
      </div>

      <div class="mt-6 bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Database Schema</h3>
        <div id="schema-tree-container" class="w-full" style="height: 600px; overflow: hidden;"></div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import ApexTree from 'apextree'

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
})

const { get } = useApi()
const loading = ref(true)
const stats = ref({ users: 0, groups: 0, permissions: 0, recentActivity: 0 })
const charts = ref({
  usersPerGroup: [] as { name: string; count: number }[],
  permissionsPerContentType: [] as { app_label: string; model: string; count: number }[],
  activityLast7Days: [] as { date: string; count: number }[],
})

const colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1']

const pieOptions = {
  labels: [] as string[],
  chart: { type: 'pie' as const },
  colors,
  legend: { position: 'bottom' as const, fontSize: '13px', itemMargin: { horizontal: 12 } },
  dataLabels: { enabled: false },
  responsive: [{ breakpoint: 480, options: { chart: { height: 240 }, legend: { position: 'bottom' } } }],
}


const areaOptions = {
  chart: {
    type: 'area' as const,
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  colors: ['#3b82f6'],
  fill: {
    type: 'gradient',
    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100] },
  },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth' as const, width: 2 },
  xaxis: { type: 'category' as const, axisBorder: { show: false }, axisTicks: { show: false }, categories: [] as string[] },
  yaxis: { min: 0, forceNiceScale: true },
  grid: { borderColor: '#f1f5f9', padding: { left: 0, right: 0 } },
  tooltip: { enabled: true },
  legend: { show: false },
}

const usersPerGroupSeries = computed(() => {
  const data = charts.value.usersPerGroup
  pieOptions.labels = data.map(g => g.name)
  return data.map(g => g.count)
})


const permsPerTypeSeries = computed(() => {
  const data = charts.value.permissionsPerContentType
  pieOptions.labels = data.map(p => `${p.app_label} / ${p.model}`)
  return data.map(p => p.count)
})

const activitySeries = computed(() => {
  const data = charts.value.activityLast7Days
  areaOptions.xaxis = { ...areaOptions.xaxis, categories: data.map(a => a.date) }
  return [{ name: 'Actions', data: data.map(a => a.count) }]
})

onMounted(async () => {
  const { data } = await get<{ stats: typeof stats.value; charts: typeof charts.value & { userTree?: any; schemaTree?: any } }>('/dashboard/stats')
  if (data) {
    stats.value = data.stats
    if (data.charts) {
      charts.value = data.charts
      
      if (data.charts.userTree) {
        const transformTree = (node: any, level: number = 0): any => {
          const getInitials = (name: string) => {
            return name.split(' ').map((n: string) => n[0]).join('').substring(0, 2).toUpperCase() || 'U'
          }

          let color = 'blue'
          let badge = 'SYSTEM'
          let subtitle = 'Application'
          
          if (level === 1) {
            color = 'green'
            badge = 'GROUP'
            subtitle = 'Role Definition'
          } else if (level === 2) {
            color = 'orange'
            badge = 'USER'
            subtitle = 'Active Member'
          }

          return {
            id: node.id,
            data: {
              name: node.name,
              initials: getInitials(node.name),
              color,
              badge,
              subtitle
            },
            children: node.children ? node.children.map((c: any) => transformTree(c, level + 1)) : []
          }
        }

        const formattedTree = transformTree(data.charts.userTree)

        setTimeout(() => {
          const treeContainer = document.getElementById('apex-tree-container')
          if (treeContainer) {
            treeContainer.innerHTML = ''
            const treeOptions = {
              width: '100%',
              height: 500,
              direction: 'left' as const,
              contentKey: 'data',
              nodeWidth: 210,
              nodeHeight: 56,
              childrenSpacing: 60,
              siblingSpacing: 16,
              canvasStyle: 'background: transparent;',
              nodeTemplate: (content: any) => {
                let borderClass = 'border-blue-200'
                let bgClass = 'bg-blue-100'
                let textClass = 'text-blue-700'
                let badgeBg = 'bg-blue-500'
                
                if (content.color === 'green') {
                  borderClass = 'border-emerald-200'
                  bgClass = 'bg-emerald-100'
                  textClass = 'text-emerald-700'
                  badgeBg = 'bg-emerald-500'
                } else if (content.color === 'orange') {
                  borderClass = 'border-amber-200'
                  bgClass = 'bg-amber-100'
                  textClass = 'text-amber-700'
                  badgeBg = 'bg-amber-500'
                }

                return `
                  <div class="flex items-center justify-between w-[210px] h-[56px] p-2 bg-white border-2 ${borderClass} rounded-xl shadow-sm box-border">
                    <div class="flex items-center gap-2 w-full max-w-[130px]">
                      <div class="flex items-center justify-center min-w-8 min-h-8 w-8 h-8 ${bgClass} rounded-full shrink-0">
                        <span class="text-xs font-bold ${textClass}">${content.initials}</span>
                      </div>
                      <div class="flex flex-col truncate">
                        <span class="text-[12px] font-bold text-slate-800 leading-tight truncate">${content.name}</span>
                        <span class="text-[9px] text-slate-500 mt-0.5 truncate">${content.subtitle}</span>
                      </div>
                    </div>
                    <div class="px-1.5 py-0.5 ${badgeBg} rounded-full shrink-0 ml-1">
                      <span class="text-[8px] font-bold text-white uppercase tracking-wider">${content.badge}</span>
                    </div>
                  </div>
                `
              }
            }
            const tree = new ApexTree(treeContainer, treeOptions)
            tree.render(formattedTree)
          }
        }, 100)
      }

      if (data.charts.schemaTree) {
        const transformSchemaTree = (node: any, level: number = 0): any => {
          const getInitials = (name: string) => {
            return name.substring(0, 2).toUpperCase()
          }

          let color = 'blue'
          let badge = 'DATABASE'
          let subtitle = 'Symfony ACL'
          
          if (level === 1) {
            color = 'green'
            badge = 'TABLE'
            subtitle = 'Entity'
          } else if (level === 2) {
            color = 'orange'
            badge = node.type ? node.type.toUpperCase() : 'COLUMN'
            subtitle = 'Field'
          }

          return {
            id: node.id,
            data: {
              name: node.name,
              initials: getInitials(node.name),
              color,
              badge,
              subtitle
            },
            children: node.children ? node.children.map((c: any) => transformSchemaTree(c, level + 1)) : []
          }
        }

        const formattedSchemaTree = transformSchemaTree(data.charts.schemaTree)

        setTimeout(() => {
          const schemaTreeContainer = document.getElementById('schema-tree-container')
          if (schemaTreeContainer) {
            schemaTreeContainer.innerHTML = ''
            const schemaTreeOptions = {
              width: '100%',
              height: 600,
              direction: 'left' as const,
              contentKey: 'data',
              nodeWidth: 210,
              nodeHeight: 56,
              childrenSpacing: 60,
              siblingSpacing: 16,
              canvasStyle: 'background: transparent;',
              nodeTemplate: (content: any) => {
                let borderClass = 'border-blue-200'
                let bgClass = 'bg-blue-100'
                let textClass = 'text-blue-700'
                let badgeBg = 'bg-blue-500'
                
                if (content.color === 'green') {
                  borderClass = 'border-emerald-200'
                  bgClass = 'bg-emerald-100'
                  textClass = 'text-emerald-700'
                  badgeBg = 'bg-emerald-500'
                } else if (content.color === 'orange') {
                  borderClass = 'border-amber-200'
                  bgClass = 'bg-amber-100'
                  textClass = 'text-amber-700'
                  badgeBg = 'bg-amber-500'
                }

                return `
                  <div class="flex items-center justify-between w-[210px] h-[56px] p-2 bg-white border-2 ${borderClass} rounded-xl shadow-sm box-border">
                    <div class="flex items-center gap-2 w-full max-w-[130px]">
                      <div class="flex items-center justify-center min-w-8 min-h-8 w-8 h-8 ${bgClass} rounded-full shrink-0">
                        <span class="text-xs font-bold ${textClass}">${content.initials}</span>
                      </div>
                      <div class="flex flex-col truncate">
                        <span class="text-[12px] font-bold text-slate-800 leading-tight truncate">${content.name}</span>
                        <span class="text-[9px] text-slate-500 mt-0.5 truncate">${content.subtitle}</span>
                      </div>
                    </div>
                    <div class="px-1.5 py-0.5 ${badgeBg} rounded-full shrink-0 ml-1">
                      <span class="text-[8px] font-bold text-white uppercase tracking-wider">${content.badge}</span>
                    </div>
                  </div>
                `
              }
            }
            const schemaTree = new ApexTree(schemaTreeContainer, schemaTreeOptions)
            schemaTree.render(formattedSchemaTree)
          }
        }, 100)
      }
    }
  }
  loading.value = false
})
</script>
