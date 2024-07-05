/**
 * メタタグから記事要約を取得する
 * @returns {string | undefined}
 */
const getSummaryFromMeta = () => {
	return document
		.querySelector('meta[name="記事要約"]')
		?.getAttribute("content");
};

/**
 * メタタグから投稿IDを取得する
 * @returns {string | undefined}
 */
const getPostIdFromMeta = () => {
	return document
		.querySelector('meta[name="post_id"]')
		?.getAttribute("content");
};

/**
 * 記事のタイトルを取得する
 * @returns {string | undefined}
 */
const getTitleFromElement = () => {
	return document.querySelector(".wp-block-post-title")?.textContent;
};

/**
 * Summaryを保存するディレクトリを取得する
 * @returns {Promise<void|FileSystemDirectoryHandle>}
 */
const getSummaryDirectory = async () => {
	const opfsRoot = await navigator.storage.getDirectory();
	return opfsRoot.getDirectoryHandle("shape-sns-summary", { create: true });
};

/**
 * URLからクエリパラメータやアンカーを除去する
 * @param {string} url
 * @returns {string}
 */
const sanitizeUrl = (url) => {
	const urlObj = new URL(url);
	return `${urlObj.origin}${urlObj.pathname}`;
};

/**
 * 履歴を保存する
 * @param {string} summary
 * @param saveFunction
 * @returns {Promise<void>}
 */
const savePageHistory = async (summary, saveFunction = defaultSaveFunction) => {
	const url = window.location.href;
	const createdAt = new Date().toISOString();
	const title = getTitleFromElement(); // タイトルを取得

	const data = { url, summary, title, created_at: createdAt };

	const summaryDirectory = await getSummaryDirectory();
	const fileHandle = await summaryDirectory.getFileHandle("summary.json", {
		create: true,
	});

	const file = await fileHandle.getFile();
	const text = await file.text();

	let json;
	try {
		json = JSON.parse(text);
	} catch (error) {
		console.error("Failed to parse JSON", error);
		json = [];
	}

	const stack = Array.isArray(json) ? json : [json];

	const existingEntry = stack.find((item) => item.url === url);
	if (existingEntry) {
		existingEntry.view_count = (existingEntry.view_count || 0) + 1;
		existingEntry.timestamps = existingEntry.timestamps || [];
		existingEntry.timestamps.push(createdAt);
	} else {
		data.view_count = 1;
		data.timestamps = [createdAt];
		stack.unshift(data);
	}

	await saveFunction(fileHandle, stack);
};
const defaultSaveFunction = async (fileHandle, stack) => {
	const writable = await fileHandle.createWritable();
	await writable.write(JSON.stringify(stack, null, 2));
	await writable.close();
};

/**
 * 履歴を取得する
 * @returns {Promise<Array>}
 */
const fetchPageHistory = async () => {
	const summaryDirectory = await getSummaryDirectory();
	const fileHandle = await summaryDirectory.getFileHandle("summary.json", {
		create: true,
	});
	const file = await fileHandle.getFile();
	const text = await file.text();
	const json = JSON.parse(text);
	return Array.isArray(json) ? json : [json];
};

const removePageHistory = async (url) => {
	const summaryDirectory = await getSummaryDirectory();
	const fileHandle = await summaryDirectory.getFileHandle("summary.json", {
		create: true,
	});
	const file = await fileHandle.getFile();
	const text = await file.text();
	const json = JSON.parse(text);
	const stack = Array.isArray(json) ? json : [json];

	const index = stack.findIndex((item) => item.url === url);
	if (index >= 0) {
		stack.splice(index, 1);
	}

	const writable = await fileHandle.createWritable();
	await writable.write(JSON.stringify(stack, null, 2));
	await writable.close();
};

const removeAllPageHistory = async () => {
	const summaryDirectory = await getSummaryDirectory();
	const fileHandle = await summaryDirectory.getFileHandle("summary.json", {
		create: true,
	});
	const writable = await fileHandle.createWritable();
	await writable.write("[]");
	await writable.close();
};

const renderFloatingHistoryStack = (
	data,
	renderFunction = defaultRenderFunction,
) => {
	const floatingWindow = document.createElement("div");
	floatingWindow.id = "floating-history-stack";
	floatingWindow.classList.add("floating-window");

	const closeButton = document.createElement("button");
	closeButton.textContent = "Close History";
	closeButton.classList.add("floating-close-button");

	const stack = document.createElement("div");
	stack.id = "floating-history-content";
	for (const item of data.slice(0, 10)) {
		const dom = renderFunction(item);
		stack.appendChild(dom);
	}

	floatingWindow.appendChild(closeButton);
	floatingWindow.appendChild(stack);

	document.body.appendChild(floatingWindow);

	const toggleButton = document.createElement("button");
	toggleButton.textContent = "Open History";
	toggleButton.classList.add("floating-toggle-button");
	toggleButton.onclick = () => {
		if (
			floatingWindow.style.display === "none" ||
			!floatingWindow.style.display
		) {
			floatingWindow.style.display = "block";
			toggleButton.textContent = "Close History";
			toggleButton.classList.remove("open");
			toggleButton.classList.add("close");
		} else {
			floatingWindow.style.display = "none";
			toggleButton.textContent = "Open History";
			toggleButton.classList.remove("close");
			toggleButton.classList.add("open");
		}
	};

	document.body.appendChild(toggleButton);

	// 初期状態ではフローティングウィンドウを非表示にする
	floatingWindow.style.display = "none";
};

/**
 * 履歴を表示するためのスタックを挿入する
 * @param {string} id
 * @param {Array} data
 * @param {Function} renderFunction
 */
const renderHistoryStack = (
	id,
	data,
	renderFunction = defaultRenderFunction,
) => {
	const target = document.getElementById(id);
	if (!target) return;

	const stack = document.createElement("div");
	stack.id = "history-stack";
	for (const item of data) {
		const dom = renderFunction(item);
		stack.appendChild(dom);
	}
	target.appendChild(stack);
};

const defaultRenderFunction = (item) => {
	const dom = document.createElement("div");
	dom.classList.add("history-item");

	const title = document.createElement("h3");
	title.classList.add("history-title");
	title.textContent = item?.title || "No Title";

	const summary = document.createElement("p");
	summary.textContent = item?.summary || "No Summary";
	summary.classList.add("history-summary");

	const url = document.createElement("a");
	url.textContent = item?.url || "No URL";
	url.href = item.url;
	url.classList.add("history-url");

	const viewCount = document.createElement("p");
	viewCount.textContent = `Views: ${item?.view_count || 0}`;
	viewCount.classList.add("history-view-count");

	const deleteButton = document.createElement("button");
	deleteButton.textContent = "Delete";
	deleteButton.classList.add("history-delete-button");
	deleteButton.onclick = async () => {
		await removePageHistory(item.url);
		dom.remove();
	};

	dom.appendChild(title);
	dom.appendChild(summary);
	dom.appendChild(url);
	dom.appendChild(viewCount);
	dom.appendChild(deleteButton);

	return dom;
};
const initHistoryTracking = async (
	summaryGetter = getSummaryFromMeta,
	historySaver = savePageHistory,
	historyGetter = fetchPageHistory,
	historyRenderer = renderHistoryStack,
	floatingHistoryRenderer = renderFloatingHistoryStack,
) => {
	const summary = summaryGetter();
	if (!summary) return;

	const id = "shape-sns-history-stack";
	try {
		await historySaver(summary);
		console.debug("Save history successfully");

		const data = await historyGetter();
		const target = document.getElementById(id);
		if (target) {
			historyRenderer(id, data);
			console.log("Insert history stack successfully");
		} else {
			floatingHistoryRenderer(data);
			console.log("Floating history stack displayed");
		}
	} catch (error) {
		console.error("Failed to save history", error);
	}
};

document.addEventListener("DOMContentLoaded", () => {
	initHistoryTracking();
});
