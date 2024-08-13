import slideshow from '../../api/Slideshow'
import { Axios } from '../../api'
import QS from 'qs'

// initial state
const state = {
    all: [],
    slides: {},
    locked: false,
    remainingPages: 1,
    totalSlideshows: 0,
    fetchingAll: false,
    currentId: null
}

const slideshowStub = () => {
    return {
        title: '',
        theme: '',
        id: '',
        settings: {},
        slides: {}
    }
}

// getters
const getters = {
    getCurrent: (state, getters) => {
        if (!state.all.length) return slideshowStub()
        const current = state.all.find(slideshow => (slideshow.id === state.currentId))
        if (!current) return slideshowStub()
        return current
    },
}

// actions
const actions = {
    getSingleSlideshow({commit}, id) {
        slideshow.single(id).then(({data}) => {
            commit('addSlideshows', data.data)
        }).catch(error => {
            window.metaslider.app.MetaSlider.notifyError('metaslider/fetching-single-slideshows-error', error, true)
        })
    },
    getRecentSlideshows({commit}) {
        const page = 1
        slideshow.all(page).then(({data}) => {

            if (data.data.hasOwnProperty('remaining_pages')) {
                commit('updateRemainingPagesCount', data.data.remaining_pages)
                delete data.data.remaining_pages
                delete data.data.page
            }
            if (data.data.hasOwnProperty('totalSlideshows')) {
                commit('setTotalSlideshows', data.data.totalSlideshows)
                delete data.data.totalSlideshows
            }

            commit('addSlideshows', data.data)

        }).catch(error => {
            window.metaslider.app.MetaSlider.notifyError('metaslider/fetching-recent-slideshows-error', error, true)
        })
    },
    getAllSlideshows({commit}) {
        const page = 1
        return fetchAllSlideshows(page, commit).catch(error => {
            window.metaslider.app.MetaSlider.notifyError('metaslider/fetching-all-slideshows-error', error, true)
        })
    }
}

const fetchAllSlideshows = (page, commit) => {
    commit('setFetchingAll', true)
    return new Promise((resolve) => {
        slideshow.all(page, 200).then(({data}) => {
            let nextPage = false

            // If there are remaining pages we need to send another request with the next page
            if (data.data.hasOwnProperty('remaining_pages')) {
                nextPage = data.data.page + 1
                commit('updateRemainingPagesCount', data.data.remaining_pages)
                delete data.data.remaining_pages
                delete data.data.page
            } else {
                commit('updateRemainingPagesCount', 0)
            }

            if (data.data.hasOwnProperty('totalSlideshows')) {
                commit('setTotalSlideshows', data.data.totalSlideshows)
                delete data.data.totalSlideshows
            }

            commit('addSlideshows', data.data)


            let slides = {}
            let slide, slideshow

            Object.keys(data.data).forEach(slideshowKey => {
                slideshow = data.data[slideshowKey]

                for (let i = 0; i < slideshow.slides.length; i++) {
                    slide = slideshow.slides[i]

                    slides[slide.id] = {
                        'id': slide.id,
                        'thumbnail': slide.thumbnail,
                        'meta': slide.meta,
                    }
                }
            })

            commit('addSlides', slides)

            // Only make a request every 2 seconds to cut down on processing load
            setTimeout(() => {
                !nextPage && commit('setFetchingAll', false)
                resolve(nextPage ? fetchAllSlideshows(nextPage, commit) : data)
            }, 2000);
        })
    })
}

// mutations
const mutations = {
    setCurrent(state, id) {
        state.currentId = id
    },
    setTotalSlideshows(state, count) {
        state.totalSlideshows = count
    },
    updateRemainingPagesCount(state, count) {
        state.remainingPages = count
    },
    addSlideshows(state, slideshows) {
        slideshows && Object.keys(slideshows).forEach(key => {

            // Check if the slideshow already exists in the store
            const index = state.all.findIndex(slideshow => (slideshow.id === slideshows[key].id))
            if (index > -1) {
                // If the two objects are not identical, replace with the new one
                if (JSON.stringify(state.all[index]) !== JSON.stringify(slideshows[key])) {
                    Object.assign(state.all[index], slideshows[key])
                    console.log('MetaSlider:', 'Updated slideshow id #' + slideshows[key].id + ' in local storage.')
                }
            } else {

				// It's new, so push to the store
				state.all.push(slideshows[key])

				// Add sample images to the new slideshow
				if(window.location.href.indexOf('metaslider_add_sample_slides') > -1) {

                    // Get value from param in URL for metaslider_add_sample_slides
                    var slug = new URLSearchParams(window.location.search).get('metaslider_add_sample_slides');

                    // Show the notice while the slides creation runs on the background
                    document.getElementById('loading-add-sample-slides-notice').style.display = 'flex';

                    // If the slug is included in metaslider.quickstart_slugs (is a Pro demo import)
                    if (metaslider.quickstart_slugs.includes(slug)) {
                        // Import sample data from Pro
                        Axios.post('import/others', QS.stringify({
                            action: 'ms_import_others',
                            slideshow_id: slideshows[key].id,
                            slug: slug
                        })).then(response => {
                            // This will trigger sampleSlidesWereAdded()
                            window.location.href = window.location.href.replace('metaslider_add_sample_slides', 'metaslider_add_sample_slides_after');
                        }).catch(error => {
                            console.log(error)
                        })

                    } else {
                        // Import sample data from Free (just image slides)
                        Axios.post('import/images', QS.stringify({
                            action: 'ms_import_images',
                            slideshow_id: slideshows[key].id,
                        })).then(response => {
                            // This will trigger sampleSlidesWereAdded()
                            window.location.href = window.location.href.replace('metaslider_add_sample_slides', 'metaslider_add_sample_slides_after');
                        }).catch(error => {
                            console.log(error)
                        })
                    }

				}
			}
		})
	},
	updateTheme(state, theme) {
		const index = state.all.findIndex(slideshow => (slideshow.id === state.currentId))
		state.all[index]['theme'] = theme
	},
	updateTitle(state, title) {
		const index = state.all.findIndex(slideshow => (slideshow.id === state.currentId))
		state.all[index]['title'] = title
	},
	setLocked(state, locked) {
		state.locked = locked
	},
	setFetchingAll(state, status) {
		state.fetchingAll = status
	}
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
