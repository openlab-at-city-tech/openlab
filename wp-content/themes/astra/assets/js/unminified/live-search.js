/**
 * Astra's Live Search
 *
 * @package Astra
 * @since x.x.x
 */

(function () {
	function decodeHTMLEntities(string) {
		var doc = new DOMParser().parseFromString(string, "text/html");
		return doc.documentElement.textContent;
	}

	function getSearchResultPostMarkup(resultsData) {
		const fragment = document.createDocumentFragment();

		Object.entries(resultsData).forEach(([postType, postsData]) => {
			let postTypeLabel = astra_search.search_post_types_labels[postType]
				? astra_search.search_post_types_labels[postType]
				: postType + "s";

			// postTypeLabel is esc_html()'d server-side; decode it back for the textContent sink so entities display correctly.
			const heading = document.createElement("label");
			heading.className = "ast-search--posttype-heading";
			heading.textContent = decodeHTMLEntities(postTypeLabel);
			fragment.appendChild(heading);

			postsData.forEach((post) => {
				const headerCoverSearch = document.querySelector(".ast-search-box.header-cover");
				const fullScreenSearch = document.getElementById("ast-seach-full-screen-form");

				const item = document.createElement("a");
				item.className = "ast-search-item";
				item.setAttribute("role", "option");
				item.setAttribute("target", "_self");
				// setAttribute stores post.link as a literal value, so it can never break out of the markup.
				item.setAttribute("href", post.link);
				if (fullScreenSearch || headerCoverSearch) {
					item.setAttribute("tabindex", "1");
				}

				const titleWrap = document.createElement("span");
				// textContent never parses its input as HTML, so a crafted post title stays inert text.
				titleWrap.textContent = decodeHTMLEntities(post.title.rendered);

				item.appendChild(titleWrap);
				fragment.appendChild(item);
			});
		});

		return fragment;
	}

	window.addEventListener("load", function (e) {
		const searchInputs = document.querySelectorAll(".search-field");
		searchInputs.forEach((searchInput) => {
			searchInput.addEventListener("input", function (event) {
				const searchForm = searchInput.closest("form.search-form");
				const searchTerm = event.target.value.trim();
				const postTypes = astra_search.search_page_condition ? astra_search.search_page_post_types : astra_search.search_post_types;

				const searchResultsWrappers = document.querySelectorAll(
					".ast-live-search-results"
				);
				if (searchResultsWrappers) {
					searchResultsWrappers.forEach(function (wrap) {
						wrap.parentNode.removeChild(wrap);
					});
				}

				try {
					const restRequest = `${
						astra_search.rest_api_url
					}wp/v2/posts${
						astra_search.rest_api_url.indexOf("?") > -1 ? "&" : "?"
					}_embed=1&post_type=ast_queried:${postTypes.join(
						":"
					)}&per_page=${
						astra_search.search_posts_per_page
					}&search=${searchTerm}${
						astra_search.search_language
							? `&lang=${astra_search.search_language}`
							: ""
					}`;

					var xhr = new XMLHttpRequest();
					xhr.open("GET", restRequest, true);
					xhr.onreadystatechange = function () {
						if (xhr.readyState === 4 && xhr.status === 200) {
							const postsData = JSON.parse(xhr.responseText);

							const resultsContainer = document.createElement("div");
							resultsContainer.className = "ast-live-search-results";
							resultsContainer.setAttribute("role", "listbox");
							resultsContainer.setAttribute("aria-label", astra_search.search_results_label);
							resultsContainer.style.top = parseInt(searchForm.offsetHeight) + 10 + "px";

							if (postsData.length > 0) {
								let formattedPostsData = {};
								postsData.forEach((post) => {
									if (post.type in formattedPostsData) {
										formattedPostsData[post.type].push(
											post
										);
									} else {
										formattedPostsData[post.type] = [post];
									}
								});
								resultsContainer.appendChild(
									getSearchResultPostMarkup(
										formattedPostsData
									)
								);
							} else {
								const noResults = document.createElement("label");
								noResults.className = "ast-search--no-results-heading";
								// no_live_results_found is a server-provided translated string.
								noResults.textContent = astra_search.no_live_results_found;
								resultsContainer.appendChild(noResults);
							}

							const searchResultsWrappers =
								document.querySelectorAll(
									".ast-live-search-results"
								);
							if (searchResultsWrappers) {
								searchResultsWrappers.forEach(function (wrap) {
									wrap.parentNode.removeChild(wrap);
								});
							}
							searchForm.appendChild(resultsContainer);
						}
					};

					xhr.send();
				} catch (error) {
					console.error("Error while fetching data:", error);
				}
			});
		});
	});

	// Add a click event listener to the document.
	document.addEventListener("click", function (event) {
		const searchForm = event.target.closest("form.search-form");

		// Check if the clicked element is the search bar or the results dropdown
		if (null !== searchForm) {
			// Clicked inside the search bar or dropdown, do nothing
			if (searchForm.querySelector(".ast-live-search-results")) {
				searchForm.querySelector(
					".ast-live-search-results"
				).style.display = "block";
			}
		} else {
			// Clicked outside the search bar and dropdown, hide the dropdown
			const searchResultsWrappers = document.querySelectorAll(
				".ast-live-search-results"
			);
			if (searchResultsWrappers) {
				searchResultsWrappers.forEach(function (wrap) {
					wrap.style.display = "none";
				});
			}
		}
	});
})();
