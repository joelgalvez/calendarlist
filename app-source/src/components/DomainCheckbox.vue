<template>
	<div :class="{'domain-checkbox':true,'disabled':disabled,'enabled':!disabled}" >
		<div class="dot" :style="'background-color:' + color + ';'"></div>
		<div :class="{'checkbox':true,'checked':checked}" @click="onClick">
			<svg v-if="!loading" class="cross" viewBox="0 0 100 100">
				<line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke"></line>
				<line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke"></line>
			</svg>
			<svg class="loading" v-if="loading" viewBox="0 0 100 100" xml:space="preserve">
				<line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke"></line>
				<line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke"></line>
			</svg>
		</div>
		<div class="text">
			<div class="title">{{title}} </div>
			<div class="website">
				<div class="url">{{domainSansPrefix}}</div>
			</div>
			<div class="title"><span class="status" v-html="status"></span> <span v-if="alternativeSource" class="alternative-source"> — from {{alternativeSource}}</span></div>
			<div class="error-status" v-html="errorStatus"></div>
		</div>
		<!-- <div class="padlock" @click="onPadlock">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
			<path fill="#999" d="M8 10v-4c0-2.206 1.795-4 4-4s4 1.794 4 4v1h2v-1c0-3.313-2.687-6-6-6s-6 2.687-6 6v4h-3v14h18v-14h-13zm11 12h-14v-10h14v10z"/></svg>
		</div> -->
	</div>
		
</template>

<script>
export default {
	name: 'DomainCheckbox',
	props: {
		domain: String,
		checked: Boolean,
		disabled: {
			default: false,
			type: Boolean
		},
		loading: {
			default: false,
			type: Boolean
		},
		color: String,
		disabled: {
			default: false,
			type: Boolean
		},
		error: {
			default: false,
			type: Boolean
		},
		title: String,
		status: String,
		errorStatus: String,
		alternativeSource: String
	},
	data() {
		return {
			
		}
	},
	methods: {
		onClick() {
			this.$emit('onClick');
		},
		onPadlock() {
			alert("This calendar is unverfied, because the verification process isn\'t working yet.\nFor now you have to trust this website and the list owner that this calendar actually is what it says it is.");
		}

	},
	computed: {
		domainSansPrefix() {
			return this.domain.replace('https://', '').replace('http://', '');
		}
	}
}
</script>

<style scoped lang="scss">

	@keyframes rotate {
		0% {
			    transform: rotate(0deg);
		}
		100% {
			    transform: rotate(3600deg);
		}
	}
	.domain-checkbox {
		display:flex;
		align-items: flex-start;
		// min-height:4rem;
		padding:0.5em;
		&.enabled:hover {
			// background-color: rgb(244, 244, 244);
		}
		.checkbox {
			cursor: pointer;
			width:1.2em;
			height:1.2em;
			border: 1px solid;
			margin-right: 0.5rem;
			flex: 0 0 auto;
			border-radius:0.15rem;
			position:relative;
			

			.loading {
				animation: rotate 20s linear infinite;
			}
			.loading,
			.cross {
				position: absolute;
			}
			&.checked {
				svg.cross {
					display:block;
				}
			}
			
			svg {
				&.cross {
					display:none;
				}
				position: absolute;
				
				width:100%;
				height:100%;
				stroke-width: 1.7px;
				stroke: black;
				padding:3px;
			}
		}
		.padlock {
			cursor: pointer;
		}
		.text {
			min-width: 0;
			.title {
				font-weight: 500;
				
			}
			.website {
				// color:gray;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				.url {
					white-space: nowrap;
					overflow: hidden;
					text-overflow: ellipsis;
					
				}
			}
			.website,
			.error-status,
			.status {
				font-size: 0.7rem;

			}
			.status {
				color:blue;
			}
			.error-status {
				color:red;
			}
			.alternative-source {
				color:rgb(92, 92, 92);
			}
		}

		&.disabled {
			color:#7d7d7d;
			pointer-events: none;
			.checkbox {
				border-color: rgb(189, 189, 189);
				background-color: rgb(226, 226, 226);
			}
		}
		.dot {
			width:0.60rem;
			height:0.60rem;
			border-radius:50%;
			margin-right:0.75rem;
			margin-top:0.30rem;
			flex: 0 0 auto;
		}
		.checkbox-container {
			display:block;
			user-select: none;
			position:relative;
			
			
			input {
				position: absolute;
				opacity: 0;
				cursor: pointer;
				height: 0;
				width: 0;
				
			}
			.checkmark {
				position: absolute;
				top: 0;
				left: 0;
				height: 1em;
				width: 1em;
				border:2px solid;
			
			}
		}

		input {
			width:1rem;
			margin-right:0.5rem;
			flex: 0 0 auto;
		}
		.text {
			flex: 1 1 auto;

		}
	}
</style>
