const jsonObject = obj => {
    try {
        JSON.parse(obj)
        return true;
    } catch (e) {
        console.log(e)
        return false;
    }
}

const showVal = (id, newValue) => $(id).text(newValue);
const removeErrors = () => $('label.error').remove();
const focus_first_ele = () => $('.error:first').focus();
const changeClass = (id, name, exchange) => {
    $(`#${id}`).removeClass(`${name}`);
    $(`#${id}`).addClass(`${exchange}`);
}
$('[data-toggle="tooltip"]').tooltip();

const display_errors = obj => {
    removeErrors();
    $.map(JSON.parse(obj), (value, key) => {
        let label = `<label id="${key}-error" class="error" for="${key}">${value}</label>`
        $(label).insertAfter(`#${key}`);
        changeClass(key, 'valid', 'error');
    });
    focus_first_ele();
}

let searchParams = new URLSearchParams(window.location.search);

$(document).ready(() => {
    // slide slow our navigation bar for mobiles
    $('body').on('.menu', 'click', () => {
        $('ul').slideToggle('slow', () => $('ul').toggleClass('active'))
    })

    //delete a href for story in administrative page
    $('#collapseOne').find('a.delete').remove();
    // Submit contact form
    const contactForm = $("#form").validate({
        rules: {
            title: {
                required: ($('#title').val() === '')
            },
            email: {
                required: true,
                email: true
            }
        },
        submitHandler: function (form) {
            $.ajax({
                url: 'index.php?page=contact',
                type: 'POST',
                data: $(form).serialize(),
                success: function (data) {
                    if (jsonObject(data)) {
                        display_errors(data);
                        return false;
                    }
                    contactForm.destroy();
                    window.location.reload();
                },
                error: function (data) {
                    let paragraphElement = "<p class='message-error'></p>";
                    ($('.message-error') !== 'undefined') ? $('.message-error').append('</br> New message') : $('span.submit-message').append(paragraphElement);
                    console.log(data);
                }
            });
            return false;
        }
    })

    // Login credential and form
    $('body').on('click', '.login', () => {
        const loginForm = $('#loginForm').validate({
            rules: {
                mail: {
                    required: true
                },
                pass: {
                    required: true
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "index.php?page=login",
                    type: "POST",
                    data: $(form).serialize(),
                    success: function (data) {
                        console.log(data)
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        loginForm.destroy();
                        window.location.reload();
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // Reset your password
    $('body').on('click', '.resetPass', () => {
        $('#confirmPassword').validate({
            rules: {
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 12
                },
                confirmPass: {
                    required: true,
                    minlength: 6,
                    maxlength: 12,
                    equalTo: "#password"
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "index.php?page=reset",
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (data) {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    showVal('.knowledgeValue', $('#editKnowledge').val())
    showVal('.knowledgeValue', $('#knowledge').val())

    $(document).click(function (event) {
        const clickover = $(event.target);
        const _opened = $(".collapse").hasClass("show");

        if (_opened === true && !clickover.hasClass("navbar-toggler")) {
            $("button.navbar-toggler").click();
        }
    });

    // add Project
    $('body').on('click', '.addProject', () => {
        $('#addProjectForm').validate({
            rules: {
                editUrl: {
                    required: true,
                    url: true
                }
            },
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    projects: data,
                    action: 'add'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        console.log(data)
                        window.location.reload()
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // add Story
    $('body').on('click', '.addStory', () => {
        $('#addStoryForm').validate({
            submitHandler: function (form) {
                let fd = new FormData($(form)[0]);
                fd.append('action', 'add');
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // add Skills
    $('body').on('click', '.addSkill', () => {
        $('#addSkillForm').validate({
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    skills: data,
                    action: 'add'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // edit Project
    $('.edit').click(function () {
        let id = $(this).data('id');
        $('#editProjectModal').find('button.editProject').attr('data-id', id);
    })
    $('body').on('click', '.editProject', () => {
        $('#editProjectForm').validate({
            rules: {
                editUrl: {
                    required: true,
                    url: true
                }
            },
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    projects: data,
                    id: $('#editProjectModal').find('button.editProject').attr('data-id'),
                    action: 'edit'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        console.log(data);
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // edit Story
    $('.edit').click(function () {
        let id = $(this).data('id');
        $('#editStoryModal').find('button.editStory').attr('data-id', id);
    })
    $('body').on('click', '.editStory', () => {

        $('#editStoryForm').validate({
            submitHandler: function (form) {
                let id = $('#editStoryModal').find('button.editStory').attr('data-id'),
                    fd = new FormData($(form)[0]);
                fd.append('action', 'edit');
                fd.append('id', id.toString());
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })


    $('#editSkillsModal').on('shown.bs.modal', function () {
        // ... init all your modal here
        let id = $('#editSkillsModal').find('button.editSkill').attr('data-id'),
            color = '';
        const colorChange = color => $(`#editColor`).val(color)
        const modal={
            modalEdit: id
        }
        $.ajax({
            url: 'index.php?page=action',
            data: modal,
            type: 'post',
            success: (data) => {
                $.each($.parseJSON(data), (item, value) => {
                    if (item === 'Color') {
                        color = `#${value}`
                    }
                    $(`#edit${item}`).val(value)
                })
                showVal('.knowledgeValue', $('#editKnowledge').val())
                colorChange(color)
            }
        })
    });

    // edit Skills
    $('.edit').click(function () {
        let id = $(this).data('id');
        $('#editSkillsModal').find('button.editSkill').attr('data-id', id);
    })
    $('body').on('click', '.editSkill', () => {
        $('#editSkillForm').validate({
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    skills: data,
                    id: $('#editSkillsModal').find('button.editSkill').attr('data-id'),
                    action: 'edit'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // delete Project
    $('.delete').click(function () {
        let id = $(this).data('id');
        $('#deleteProjectModal').find('button.deleteProject').attr('data-id', id);
    })
    $('body').on('click', '.deleteProject', () => {
        const obj = {
            projects: 'delete',
            id: $('#deleteProjectModal').find('button.deleteProject').attr('data-id'),
            action: 'delete'
        }
        $.ajax({
            url: 'index.php?page=action',
            type: 'post',
            data: obj,
            success: data => {
                console.log(data)
                window.location.reload();
            },
            error: data => {
                console.log(data)
            }
        })
    })

    // delete Story
    // $('.delete').click(function () {
    //     let id = $(this).data('id');
    //     $('#deleteStoryModal').find('button.deleteStory').attr('data-id', id);
    // })
    // $('body').on('click', '.deleteStory', () => {
    //     let fd = {
    //         action: 'delete',
    //         story: 'story',
    //         id: $('#deleteStoryModal').find('button.deleteStory').attr('data-id'),
    //     };
    //     $.ajax({
    //         url: 'index.php?page=action',
    //         type: 'post',
    //         data: fd,
    //         success: data => {
    //             console.log(data)
    //             return false;
    //             if (jsonObject(data)) {
    //                 display_errors(data);
    //                 return false;
    //             }
    //             window.location.reload();
    //         },
    //         error: data => {
    //             console.log(data)
    //         }
    //     })
    // })

//delete Skills
    $('.delete').click(function () {
        let id = $(this).data('id');
        $('#deleteSkillModal').find('button.deleteSkill').attr('data-id', id);
    })
    $('body').on('click', '.deleteSkill', () => {
        const obj = {
            skills: 'delete',
            id: $('#deleteSkillModal').find('button.deleteSkill').attr('data-id'),
            action: 'delete'
        }
        $.ajax({
            url: 'index.php?page=action',
            type: 'post',
            data: obj,
            success: data => {
                window.location.reload();
            },
            error: data => {
                console.log(data)
            }
        })
    })

    if (searchParams.get('page') === 'admin') {
        $.ajax({
            url: 'index.php?page=update',
            type: 'post',
            data: {name: 'update'},
            success: (data) => {
                console.log(data)
            },
            error: data => {
                console.log(data)
            }
        });
    }

    if (searchParams.get('page') === 'skills') {
        // main svg
        const svg = d3.select("svg"),
            width = +svg.attr("width"),
            height = +svg.attr("height"),
            g = svg.append("g").attr("transform", "translate(20,0)");       // move right 20px.

        // x-scale and x-axis
        const experienceName = ["", "Basic 1.0", "Alright 2.0", "Handy 3.0", "Expert 4.0", "Guru 5.0"];
        const formatSkillPoints = function (d) {
            return experienceName[d % 6];
        };
        const xScale = d3.scaleLinear()
            .domain([0, 5])
            .range([0, 400]);

        const xAxis = d3.axisTop()
            .scale(xScale)
            .ticks(5)
            .tickFormat(formatSkillPoints);

        // Setting up a way to handle the data
        const tree = d3.cluster()                 // This D3 API method setup the Dendrogram datum position.
            .size([height, width - 550])    // Total width - bar chart width = Dendrogram chart width
            .separation(function separate(a, b) {
                return a.parent == b.parent            // 2 levels tree grouping for category
                || a.parent.parent == b.parent
                || a.parent == b.parent.parent ? 0.4 : 0.8;
            });

        const stratify = d3.stratify()            // This D3 API method gives cvs file flat data array dimensions.
            .parentId(function (d) {
                return d.id.substring(0, d.id.lastIndexOf("."));
            });

        d3.csv("media/skills.csv", row, function (error, data) {
            if (error) throw error;

            const root = stratify(data);
            tree(root);

            // Draw every datum a line connecting to its parent.
            const link = g.selectAll(".link")
                .data(root.descendants().slice(1))
                .enter().append("path")
                .attr("class", "link")
                .attr("d", function (d) {
                    return "M" + d.y + "," + d.x
                        + "C" + (d.parent.y + 100) + "," + d.x
                        + " " + (d.parent.y + 100) + "," + d.parent.x
                        + " " + d.parent.y + "," + d.parent.x;
                });

            // Setup position for every datum; Applying different css classes to parents and leafs.
            const node = g.selectAll(".node")
                .data(root.descendants())
                .enter().append("g")
                .attr("class", function (d) {
                    return "node" + (d.children ? " node--internal" : " node--leaf");
                })
                .attr("transform", function (d) {
                    return "translate(" + d.y + "," + d.x + ")";
                });

            // Draw every datum a small circle.
            node.append("circle")
                .attr("r", 4);

            // Setup G for every leaf datum.
            const leafNodeG = g.selectAll(".node--leaf")
                .append("g")
                .attr("class", "node--leaf-g")
                .attr("transform", "translate(" + 8 + "," + -13 + ")");

            leafNodeG.append("rect")
                .attr("class", "shadow")
                .style("fill", function (d) {
                    return d.data.color;
                })
                .attr("width", 2)
                .attr("height", 30)
                .attr("rx", 2)
                .attr("ry", 2)
                .transition()
                .duration(800)
                .attr("width", function (d) {
                    return xScale(d.data.value);
                });

            leafNodeG.append("text")
                .attr("dy", 19.5)
                .attr("x", 8)
                .style("text-anchor", "start")
                .text(function (d) {
                    return d.data.id.substring(d.data.id.lastIndexOf(".") + 1);
                });

            // Write down text for every parent datum
            const internalNode = g.selectAll(".node--internal");
            internalNode.append("text")
                .attr("y", -10)
                .style("text-anchor", "middle")
                .text(function (d) {
                    return d.data.id.substring(d.data.id.lastIndexOf(".") + 1);
                });

            // Attach axis on top of the first leaf datum.
            const firstEndNode = g.select(".node--leaf");
            firstEndNode.insert("g")
                .attr("class", "xAxis")
                .attr("transform", "translate(" + 2 + "," + -14 + ")")
                .call(xAxis);

            // tick mark for x-axis
            firstEndNode.insert("g")
                .attr("class", "grid")
                .attr("transform", "translate(2," + (height - 15) + ")")
                .call(d3.axisBottom()
                    .scale(xScale)
                    .ticks(5)
                    .tickSize(-height, 0, 0)
                    .tickFormat("")
                );

            // Emphasize the y-axis baseline.
            svg.selectAll(".grid").select("line")
                .style("stroke-dasharray", "20,1")
                .style("stroke", "black");

            // The moving ball
            const ballG = svg.insert("g")
                .attr("class", "ballG")
                .attr("transform", "translate(" + 1100 + "," + height / 2 + ")");
            ballG.insert("circle")
                .attr("class", "shadow")
                .style("fill", "steelblue")
                .attr("r", 5);
            ballG.insert("text")
                .style("text-anchor", "middle")
                .attr("dy", 5)
                .text("0.0");

            // Animation functions for mouse on and off events.
            d3.selectAll(".node--leaf-g")
                .on("mouseover", handleMouseOver)
                .on("mouseout", handleMouseOut);

            function handleMouseOver(d) {
                const leafG = d3.select(this);

                leafG.select("rect")
                    .attr("stroke", "#4D4D4D")
                    .attr("stroke-width", "2");


                const ballGMovement = ballG.transition()
                    .duration(400)
                    .attr("transform", "translate(" + (d.y
                        + xScale(d.data.value) + 90) + ","
                        + (d.x + 1.5) + ")");

                ballGMovement.select("circle")
                    .style("fill", d.data.color)
                    .attr("r", 18);

                ballGMovement.select("text")
                    .delay(300)
                    .text(Number(d.data.value).toFixed(1));
            }

            function handleMouseOut() {
                const leafG = d3.select(this);

                leafG.select("rect")
                    .attr("stroke-width", "0");
            }

        });

        function row(d) {
            return {
                id: d.id,
                value: +d.value,
                color: d.color
            };
        }

    }// end skills page setting

})// End of document listen