<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inbox') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div x-data="{ 
                        filter: 'all',
                        broadcasts: {{ Js::from($broadcasts) }},
                        get counts() {
                            return {
                                all: this.broadcasts.length,
                                new: this.broadcasts.filter(b => b.user_state_class === 'font-semibold').length,
                                read: this.broadcasts.filter(b => b.user_state_class === 'font-normal').length,
                                archived: this.broadcasts.filter(b => b.user_state_class.includes('text-orange-500')).length,
                                deleted: this.broadcasts.filter(b => b.user_state_class === 'text-red-500').length
                            }
                        },
                        filteredBroadcasts() {
                            return this.broadcasts.filter(broadcast => {
                                if (this.filter === 'all') return true;
                                
                                const stateClass = broadcast.user_state_class;
                                switch(this.filter) {
                                    case 'new':
                                        return stateClass === 'font-semibold';
                                    case 'read':
                                        return stateClass === 'font-normal';
                                    case 'deleted':
                                        return stateClass === 'text-red-500';
                                    case 'archived':
                                        return stateClass.includes('text-orange-500');
                                    default:
                                        return true;
                                }
                            });
                        }
                    }">
                        <div class="flex justify-end mb-4">
                            <select x-model="filter" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="all" x-text="`All Messages (${counts.all})`"></option>
                                <option value="new" x-text="`New (${counts.new})`"></option>
                                <option value="read" x-text="`Read (${counts.read})`"></option>
                                <option value="archived" x-text="`Archived (${counts.archived})`"></option>
                                <option value="deleted" x-text="`Deleted (${counts.deleted})`"></option>
                            </select>
                        </div>
                    
                        <table class="table w-full">
                            <tbody>
                                <template x-for="broadcast in filteredBroadcasts()" :key="broadcast.id">
                                    <tr class="h-24 border">
                                        <td class="text-sm font-extralight align-text-top p-4 whitespace-nowrap" 
                                            x-text="new Date(broadcast.created_at).toLocaleDateString('en-US', { 
                                                weekday: 'short', 
                                                month: 'short', 
                                                day: 'numeric', 
                                                year: 'numeric' 
                                            })">
                                        </td>
                                        <td class="align-text-top p-4">
                                            <div :class="broadcast.user_state_class" class="text-md" x-text="broadcast.title"></div>
                                            <div class="font-extralight text-sm ml-3" x-text="broadcast.content"></div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>